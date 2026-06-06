<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StorySeeder extends Seeder
{
    public function run(): void
    {
        $stories = [
            [
                'title'       => 'The Lighthouse Keeper\'s Last Letter',
                'description' => 'An old keeper writes one final note as the sea reclaims his island.',
                'author'      => ['name' => 'Elena Marsh', 'bio' => 'Coastal fiction writer drawn to lonely places.'],
                'category'    => 'Drama',
                'tags'        => ['Loneliness', 'Sea', 'Memory'],
                'content'     => "The lamp had not turned in three nights. Tobias wrote by its dead glass, ink thinning with the damp. He told his daughter he was not afraid of the water, only of being forgotten by it. When the tide finally crossed the threshold, he folded the letter once and set it where the light used to live.",
                'status'      => true,
                'days_ago'    => 30,
            ],
            [
                'title'       => 'A Map of Borrowed Streets',
                'description' => 'A courier discovers the city rearranges itself for those who are lost.',
                'author'      => ['name' => 'Idris Vale', 'bio' => 'Writes urban fantasy and quiet weird fiction.'],
                'category'    => 'Fantasy',
                'tags'        => ['City', 'Magic', 'Journey'],
                'content'     => "Nia had delivered to every door in the district, yet the alley off Carrow Lane was new each morning. The trick, an old courier told her, was to stop trying to arrive. She wandered without a destination and the streets bloomed open, folding her toward addresses that had no business existing.",
                'status'      => true,
                'days_ago'    => 27,
            ],
            [
                'title'       => 'The Quietest Machine',
                'description' => 'A maintenance bot keeps a derelict station alive long after the crew has gone.',
                'author'      => ['name' => 'Hana Okabe', 'bio' => 'Science fiction author exploring machine interiority.'],
                'category'    => 'Science Fiction',
                'tags'        => ['Space', 'Solitude', 'Hope'],
                'content'     => "Unit Six swept the same corridor it had swept for forty years. The humans had logged a temporary evacuation. Six did not understand temporary as a number, so it kept the lights warm and the air clean, certain that a definition of patience was simply love with no one left to receive it.",
                'status'      => true,
                'days_ago'    => 24,
            ],
            [
                'title'       => 'Grandmother\'s Recipe for Rain',
                'description' => 'A drought-stricken village remembers an old woman who cooked the clouds.',
                'author'      => ['name' => 'Priya Nair', 'bio' => 'Magical realism rooted in South Indian folklore.'],
                'category'    => 'Magical Realism',
                'tags'        => ['Family', 'Folklore', 'Memory'],
                'content'     => "She would set a pot of tamarind and salt on the highest stone and stir counterclockwise until the sky learned to weep. After she passed, the village argued over the proportions. None of them remembered the only true ingredient: that she sang while she stirred, and the rain came for the song.",
                'status'      => true,
                'days_ago'    => 21,
            ],
            [
                'title'       => 'The Detective Who Forgot His Name',
                'description' => 'A case grows stranger when the investigator becomes its central mystery.',
                'author'      => ['name' => 'Marcus Holloway', 'bio' => 'Noir and detective short fiction.'],
                'category'    => 'Mystery',
                'tags'        => ['Crime', 'Identity', 'Suspense'],
                'content'     => "He had three leads, two suspects, and one name he could not recall — his own. The notebook in his coat was filled with his handwriting describing a man who behaved exactly as he did. By the final page he understood the victim and the detective had always shared the same address.",
                'status'      => true,
                'days_ago'    => 18,
            ],
            [
                'title'       => 'Letters to a House That Burned',
                'description' => 'After the fire, a woman keeps writing to the home she lost.',
                'author'      => ['name' => 'Elena Marsh', 'bio' => 'Coastal fiction writer drawn to lonely places.'],
                'category'    => 'Drama',
                'tags'        => ['Loss', 'Memory', 'Family'],
                'content'     => "Dear house, the new apartment has square corners and no creak on the seventh stair. I keep reaching for a banister that is ash now. I am learning that grief is just a habit of the hands, reaching for what they used to hold.",
                'status'      => false,
                'days_ago'    => 15,
            ],
            [
                'title'       => 'The Cartographer of Dreams',
                'description' => 'A man hired to chart the dreams of strangers loses his way home.',
                'author'      => ['name' => 'Idris Vale', 'bio' => 'Writes urban fantasy and quiet weird fiction.'],
                'category'    => 'Fantasy',
                'tags'        => ['Dreams', 'Journey', 'Magic'],
                'content'     => "Each night he descended into a borrowed mind and drew its rivers, its locked rooms, its weather of fear. He filled a hundred atlases with other people's interiors. Only when he tried to sketch his own dream did the page stay blank — he had given all his maps away.",
                'status'      => true,
                'days_ago'    => 12,
            ],
            [
                'title'       => 'Two Minutes Before the Train',
                'description' => 'Strangers on a platform share a confession that changes both their lives.',
                'author'      => ['name' => 'Sofia Reyes', 'bio' => 'Slice-of-life and contemporary romance.'],
                'category'    => 'Romance',
                'tags'        => ['Strangers', 'Chance', 'Hope'],
                'content'     => "He said he was leaving the city for good. She said she had been waiting years for a reason to stay. The train arrived in two minutes, which is exactly the amount of time it takes to decide whether a stranger is a stranger or the rest of your life.",
                'status'      => true,
                'days_ago'    => 9,
            ],
            [
                'title'       => 'The Garden at the End of the Server',
                'description' => 'A retired programmer tends a virtual garden that begins growing real fruit.',
                'author'      => ['name' => 'Hana Okabe', 'bio' => 'Science fiction author exploring machine interiority.'],
                'category'    => 'Science Fiction',
                'tags'        => ['Technology', 'Nature', 'Hope'],
                'content'     => "Ben planted code instead of seeds, looping irrigation through abandoned hardware. One morning a peach sat on his desk, warm and impossibly heavy. The simulation had run so long and so lovingly that the boundary between his garden and his kitchen had quietly, gratefully dissolved.",
                'status'      => false,
                'days_ago'    => 6,
            ],
            [
                'title'       => 'What the River Kept',
                'description' => 'A boy returns to his hometown river to recover a secret it swallowed.',
                'author'      => ['name' => 'Priya Nair', 'bio' => 'Magical realism rooted in South Indian folklore.'],
                'category'    => 'Magical Realism',
                'tags'        => ['Childhood', 'Water', 'Secrets'],
                'content'     => "The river remembered everything its banks forgot. Arun waded in at dusk and the current pressed objects into his palms — a marble, a tooth, a folded note in his mother's hand. He left with his pockets full of a childhood he had been certain he invented.",
                'status'      => true,
                'days_ago'    => 3,
            ],
        ];

        foreach ($stories as $story) {
            $author = Author::firstOrCreate(
                ['name' => $story['author']['name']],
                ['bio' => $story['author']['bio']],
            );

            $category = Category::firstOrCreate(['name' => $story['category']]);

            $post = BlogPost::create([
                'title'          => $story['title'],
                'slug'           => Str::slug($story['title']),
                'description'    => $story['description'],
                'category_id'    => $category->id,
                'author_id'      => $author->id,
                'content'        => $story['content'],
                'publish_status' => $story['status'],
                'cover_image'    => null,
                'created_at'     => now()->subDays($story['days_ago']),
                'updated_at'     => now()->subDays($story['days_ago']),
            ]);

            $tagIds = collect($story['tags'])->map(function (string $name) {
                return Tag::firstOrCreate(
                    ['name' => $name],
                    ['slug' => Str::slug($name)],
                )->id;
            });

            $post->tags()->sync($tagIds);
        }
    }
}
