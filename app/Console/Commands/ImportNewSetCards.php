<?php

namespace App\Console\Commands;

use App\Models\AllCard;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ImportNewSetCards extends Command
{
    protected $signature = 'cards:import-new-sets
                            {--days=2 : Number of days back to check for released sets}
                            {--force : Import even if cards already exist for the set}';

    protected $description = 'Check for newly released MTG sets and import all cards from Scryfall';

    private const SCRYFALL_SETS_URL = 'https://api.scryfall.com/sets';
    private const SCRYFALL_SEARCH_URL = 'https://api.scryfall.com/cards/search';
    private const SCRYFALL_RATE_LIMIT_MS = 100; // Scryfall asks for 50-100ms between requests

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $force = $this->option('force');

        $this->info('Checking for newly released sets...');

        $newSets = $this->getNewlyReleasedSets($days);

        if ($newSets->isEmpty()) {
            $this->info('No new sets released in the last ' . $days . ' day(s). Nothing to import.');
            return self::SUCCESS;
        }

        $this->info('Found ' . $newSets->count() . ' newly released set(s):');

        foreach ($newSets as $set) {
            $this->line("  - {$set['name']} ({$set['code']}) — released {$set['released_at']}");
        }

        $totalImported = 0;

        foreach ($newSets as $set) {
            $setCode = $set['code'];
            $setName = $set['name'];

            // Check if we already have cards for this set
            $existingCount = AllCard::where('set', $setCode)->count();

            if ($existingCount > 0 && !$force) {
                $this->warn("Set '{$setName}' ({$setCode}) already has {$existingCount} cards in the database. Skipping. Use --force to re-import.");
                continue;
            }

            $this->info("Importing cards for '{$setName}' ({$setCode})...");

            $imported = $this->importCardsForSet($setCode);

            if ($imported === false) {
                $this->error("Failed to import cards for set '{$setCode}'.");
                Log::error("[ImportNewSetCards] Failed to import cards for set '{$setCode}'.");
                continue;
            }

            $totalImported += $imported;
            $this->info("Imported {$imported} cards for '{$setName}' ({$setCode}).");
            Log::info("[ImportNewSetCards] Imported {$imported} cards for '{$setName}' ({$setCode}).");
        }

        $this->info("Done! Total cards imported: {$totalImported}");
        Log::info("[ImportNewSetCards] Completed. Total cards imported: {$totalImported}");

        return self::SUCCESS;
    }

    /**
     * Fetch sets from Scryfall and return those released within the given number of days.
     */
    private function getNewlyReleasedSets(int $days): \Illuminate\Support\Collection
    {
        $response = Http::get(self::SCRYFALL_SETS_URL);

        if (!$response->successful()) {
            $this->error('Failed to fetch sets from Scryfall API.');
            Log::error('[ImportNewSetCards] Scryfall sets API returned status: ' . $response->status());
            return collect();
        }

        $sets = collect($response->json('data'));
        $cutoffDate = Carbon::today()->subDays($days);
        $today = Carbon::today();

        return $sets->filter(function ($set) use ($cutoffDate, $today) {
            // Only consider sets that have a release date
            if (empty($set['released_at'])) {
                return false;
            }

            $releasedAt = Carbon::parse($set['released_at']);

            // Set must be released (released_at <= today) and within our lookback window
            return $releasedAt->lte($today) && $releasedAt->gte($cutoffDate);
        })->values();
    }

    /**
     * Import all cards for a given set code from Scryfall.
     */
    private function importCardsForSet(string $setCode): int|false
    {
        $imported = 0;
        $url = self::SCRYFALL_SEARCH_URL . '?q=' . urlencode("set:{$setCode}") . '&unique=prints';

        do {
            usleep(self::SCRYFALL_RATE_LIMIT_MS * 1000);

            $response = Http::get($url);

            if (!$response->successful()) {
                // A 404 means no cards found for this set, which is valid
                if ($response->status() === 404) {
                    $this->warn("No cards found on Scryfall for set '{$setCode}'.");
                    return 0;
                }

                $this->error("Scryfall API error (HTTP {$response->status()}) fetching cards for set '{$setCode}'.");
                Log::error("[ImportNewSetCards] Scryfall search API returned status: {$response->status()} for set '{$setCode}'.");
                return false;
            }

            $data = $response->json();
            $cards = $data['data'] ?? [];

            foreach ($cards as $card) {
                $this->upsertCard($card);
                $imported++;
            }

            $hasMore = $data['has_more'] ?? false;
            $url = $data['next_page'] ?? null;

            if ($hasMore) {
                $this->output->write('.');
            }
        } while ($hasMore && $url);

        if ($imported > 175) {
            $this->newLine(); // Newline after the dots
        }

        return $imported;
    }

    /**
     * Insert or update a single card from Scryfall data.
     */
    private function upsertCard(array $card): void
    {
        $imageUrl = $card['image_uris']['normal']
            ?? $card['card_faces'][0]['image_uris']['normal']
            ?? null;

        AllCard::updateOrCreate(
            [
                'name' => $card['name'],
                'lang' => $card['lang'] ?? 'en',
                'set' => $card['set'],
                'collector_number' => $card['collector_number'],
            ],
            [
                'type_line' => $card['type_line'] ?? null,
                'image_url' => $imageUrl,
            ]
        );
    }
}
