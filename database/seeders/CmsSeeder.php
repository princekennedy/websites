<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use App\Models\Content;
use App\Models\ContentBlock;
use App\Models\ContentCategory;
use App\Models\Faq;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Quiz;
use App\Models\ServiceCenter;
use App\Models\Slider;
use App\Models\User;
use App\Models\Website;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class CmsSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = collect([
            'cms.access',
            'cms.manage.categories',
            'cms.manage.contents',
            'cms.manage.faqs',
            'cms.manage.quizzes',
            'cms.manage.services',
            'cms.manage.sliders',
            'cms.manage.menus',
            'cms.manage.settings',
            'app.access.person-space',
            'app.view.private-content',
            'app.save.favorites',
            'app.ask-questions',
        ])->map(fn (string $permission): Permission => Permission::query()->updateOrCreate(
            ['name' => $permission, 'guard_name' => 'web'],
            ['name' => $permission, 'guard_name' => 'web'],
        ));

        $adminRole = Role::query()->updateOrCreate(
            ['name' => 'admin', 'guard_name' => 'web'],
            ['name' => 'admin', 'guard_name' => 'web'],
        );
        $adminRole->syncPermissions($permissions);

        $superAdminRole = Role::query()->updateOrCreate(
            ['name' => 'super-admin', 'guard_name' => 'web'],
            ['name' => 'super-admin', 'guard_name' => 'web'],
        );
        $superAdminRole->syncPermissions($permissions);

        $appUserRole = Role::query()->updateOrCreate(
            ['name' => 'user', 'guard_name' => 'web'],
            ['name' => 'user', 'guard_name' => 'web'],
        );
        $appUserRole->syncPermissions([
            'app.access.person-space',
            'app.view.private-content',
            'app.save.favorites',
            'app.ask-questions',
        ]);

        Role::query()->where('name', 'app-user')->where('guard_name', 'web')->delete();

        $samplePlatformName = config('app.name', 'Sample Platform');

        $superAdmin = User::query()->updateOrCreate(
            ['email' => 'super-admin@srhr.test'],
            [
                'name' => $samplePlatformName.' Super Admin',
                'password' => Hash::make('password'),
                'phone' => '0992383848',
                'email_verified_at' => now(),
            ],
        );
        $superAdmin->syncRoles([$superAdminRole]);

        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@srhr.test'],
            [
                'name' => $samplePlatformName.' Admin',
                'phone' => '0992383842',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );
        $admin->syncRoles([$adminRole]);

        $demoYouth = User::query()->updateOrCreate(
            ['email' => 'user@srhr.test'],
            [
                'name' => $samplePlatformName.' User',
                'phone' => '0992383841',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );
        $demoYouth->syncRoles([$appUserRole]);

        $website = Website::query()->updateOrCreate(
            ['slug' => 'default-website'],
            [
                'name' => 'Default Website',
                'domain' => null,
                'is_active' => true,
                'created_by' => $superAdmin->id,
            ],
        );

        foreach ([$superAdmin, $admin, $demoYouth] as $user) {
            $user->websites()->syncWithoutDetaching([
                $website->id => [
                    'role' => in_array($user->email, ['super-admin@srhr.test', 'admin@srhr.test'], true) ? 'owner' : 'member',
                    'is_owner' => in_array($user->email, ['super-admin@srhr.test', 'admin@srhr.test'], true),
                ],
            ]);

            $user->forceFill([
                'current_website_id' => $website->id,
            ])->save();
        }

        $categories = collect([
            [
                'name' => 'Body Literacy',
                'description' => 'Foundational guidance on puberty, menstruation, and body changes in clear, youth-friendly language.',
                'sort_order' => 1,
            ],
            [
                'name' => 'Consent and Relationships',
                'description' => 'Practical, trauma-aware information on boundaries, communication, and respectful relationships.',
                'sort_order' => 2,
            ],
            [
                'name' => 'Contraception and Family Planning',
                'description' => 'Clear, stigma-aware guidance on contraceptive choices, myths, and informed decision-making.',
                'sort_order' => 3,
            ],
            [
                'name' => 'HIV and STI Prevention',
                'description' => 'Prevention, testing, treatment literacy, and timely care-seeking information for adolescents and youth.',
                'sort_order' => 4,
            ],
            [
                'name' => 'Support and Referrals',
                'description' => 'Structured pathways for help-seeking, referral support, and essential service guidance.',
                'sort_order' => 5,
            ],
        ])->mapWithKeys(function (array $category) use ($website) {
            $model = ContentCategory::query()->updateOrCreate(
                ['website_id' => $website->id, 'slug' => str($category['name'])->slug()->value()],
                [...$category, 'website_id' => $website->id, 'is_active' => true],
            );

            return [$category['name'] => $model];
        });

        $contents = collect([
            [
                'title' => 'About the Platform',
                'content_type' => 'page',
                'category' => 'Support and Referrals',
                'summary' => 'An overview of what the platform offers, who it serves, and how it supports young people safely.',
                'body' => 'This platform brings together trusted information, service referrals, quizzes, and help pathways in one youth-friendly digital experience.',
                'audience' => 'general',
            ],
            [
                'title' => 'Privacy and Trust Online',
                'content_type' => 'page',
                'category' => 'Support and Referrals',
                'summary' => 'A clear explanation of privacy expectations, safe browsing habits, and how trust is protected in the experience.',
                'body' => 'Young people need privacy, clarity, and control. This platform is designed to reduce stigma, protect confidence, and make trusted support easier to reach.',
                'audience' => 'general',
            ],
            [
                'title' => 'Understanding Puberty Changes',
                'content_type' => 'article',
                'category' => 'Body Literacy',
                'summary' => 'An introductory guide to physical and emotional changes during puberty, written for adolescents and caregivers.',
                'body' => 'Puberty is a process, not a single moment. Young people need clear information, reassurance, and access to trusted support as their bodies and emotions change.',
                'audience' => 'adolescents',
            ],
            [
                'title' => 'Consent Starts With Communication',
                'content_type' => 'page',
                'category' => 'Consent and Relationships',
                'summary' => 'A practical explainer on consent, pressure, and healthy communication in relationships.',
                'body' => 'Consent is clear, informed, and ongoing. It cannot be assumed, forced, or borrowed from silence. Young people benefit from language that makes these ideas concrete and actionable.',
                'audience' => 'youth',
            ],
            [
                'title' => 'Choosing a Contraceptive Method',
                'content_type' => 'article',
                'category' => 'Contraception and Family Planning',
                'summary' => 'A plain-language overview of contraceptive options, side effects, and where to ask questions safely.',
                'body' => 'Different people need different options. A good guide explains short-term, long-term, barrier, and emergency methods without judgment and points users to professional support.',
                'audience' => 'youth',
            ],
            [
                'title' => 'Testing Early, Treating Early',
                'content_type' => 'article',
                'category' => 'HIV and STI Prevention',
                'summary' => 'What to expect from HIV and STI testing, why timing matters, and how to get care without delay.',
                'body' => 'Testing is part of routine health care. The best digital content reduces fear, sets expectations, and gives users practical next steps for confidential support.',
                'audience' => 'general',
            ],
            [
                'title' => 'Where to Get Help Quickly',
                'content_type' => 'service',
                'category' => 'Support and Referrals',
                'summary' => 'A referral-oriented content piece designed to point users toward trusted support and next steps.',
                'body' => 'When a young person needs help, the platform should reduce friction. This content maps common support pathways and gives language for reaching out safely.',
                'audience' => 'general',
            ],
            [
                'title' => 'Questions Young People Ask Most',
                'content_type' => 'faq',
                'category' => 'Body Literacy',
                'summary' => 'Starter FAQ content for a future public knowledge base.',
                'body' => 'Young users frequently ask whether what they are experiencing is normal, when to seek help, and how to talk to trusted adults or providers.',
                'audience' => 'youth',
            ],
            [
                'title' => 'After-Hours Help and Safety Planning',
                'content_type' => 'referral',
                'category' => 'Support and Referrals',
                'summary' => 'A short guide to emergency help-seeking, after-hours contacts, and safe next actions.',
                'body' => 'When users need urgent help, the app should surface emergency cues, trusted contacts, and the nearest relevant youth-friendly service options.',
                'audience' => 'general',
            ],
        ])->mapWithKeys(function (array $entry) use ($categories, $admin, $website) {
            $category = $categories[$entry['category']];

            $content = Content::query()->updateOrCreate(
                ['website_id' => $website->id, 'slug' => str($entry['title'])->slug()->value()],
                [
                    'website_id' => $website->id,
                    'title' => $entry['title'],
                    'summary' => $entry['summary'],
                    'body' => $entry['body'],
                    'content_type' => $entry['content_type'],
                    'category_id' => $category->id,
                    'status' => 'published',
                    'audience' => $entry['audience'],
                    'visibility' => 'public',
                    'published_at' => Carbon::now(),
                    'created_by' => $admin->id,
                    'updated_by' => $admin->id,
                ],
            );

            ContentBlock::query()->updateOrCreate(
                [
                    'website_id' => $website->id,
                    'content_id' => $content->id,
                    'block_type' => 'rich_text',
                    'sort_order' => 1,
                ],
                [
                    'website_id' => $website->id,
                    'title' => 'Intro block',
                    'body' => $entry['body'],
                    'is_active' => true,
                ],
            );

            return [$entry['title'] => $content];
        });

        $faqs = [
            [
                'question' => 'Is it normal for periods to be irregular at first?',
                'answer' => 'Yes. Cycles can be irregular in the first few years after menstruation starts. Severe pain, very heavy bleeding, or long gaps are still worth discussing with a provider.',
                'category' => 'Body Literacy',
                'audience' => 'adolescents',
                'sort_order' => 1,
            ],
            [
                'question' => 'Can someone change their mind after saying yes?',
                'answer' => 'Yes. Consent can be withdrawn at any point. Respectful relationships require listening, checking in, and stopping immediately when someone is uncomfortable.',
                'category' => 'Consent and Relationships',
                'audience' => 'youth',
                'sort_order' => 2,
            ],
            [
                'question' => 'Where can I get contraception without judgment?',
                'answer' => 'Youth-friendly clinics and trained providers should explain options privately, answer questions, and help you choose what fits your needs and preferences.',
                'category' => 'Contraception and Family Planning',
                'audience' => 'youth',
                'sort_order' => 3,
            ],
            [
                'question' => 'What should I do if I think I have an STI?',
                'answer' => 'Seek testing and treatment early. Avoid self-medicating and use the service directory to identify a nearby, youth-friendly clinic for confidential support.',
                'category' => 'HIV and STI Prevention',
                'audience' => 'general',
                'sort_order' => 4,
            ],
        ];

        foreach ($faqs as $faqData) {
            Faq::query()->updateOrCreate(
                ['website_id' => $website->id, 'slug' => str($faqData['question'])->slug()->value()],
                [
                    'website_id' => $website->id,
                    'question' => $faqData['question'],
                    'answer' => $faqData['answer'],
                    'category_id' => $categories[$faqData['category']]->id,
                    'audience' => $faqData['audience'],
                    'visibility' => 'public',
                    'sort_order' => $faqData['sort_order'],
                    'is_published' => true,
                    'created_by' => $admin->id,
                    'updated_by' => $admin->id,
                ],
            );
        }

        $services = [
            [
                'name' => 'Capital Hill Youth Wellness Centre',
                'category' => 'Support and Referrals',
                'district' => 'Lilongwe',
                'physical_address' => 'Capital Hill, Lilongwe',
                'contact_phone' => '+265 999 100 200',
                'contact_email' => 'capitalhill@srhr.test',
                'service_hours' => 'Mon-Fri 08:00-17:00',
                'summary' => 'Youth-friendly counselling, contraception guidance, STI screening, and referrals.',
                'services' => 'Counselling, contraception, STI testing, GBV referral support',
                'is_featured' => true,
            ],
            [
                'name' => 'Mzuzu Safe Access Hub',
                'category' => 'Support and Referrals',
                'district' => 'Mzuzu',
                'physical_address' => 'Luwinga, Mzuzu',
                'contact_phone' => '+265 999 300 400',
                'contact_email' => 'mzuzu@srhr.test',
                'service_hours' => 'Daily 09:00-18:00',
                'summary' => 'Drop-in services for adolescents and youth with discreet triage and referrals.',
                'services' => 'HIV testing, STI care, mental health support, referral navigation',
                'is_featured' => true,
            ],
            [
                'name' => 'Blantyre Youth Response Desk',
                'category' => 'Support and Referrals',
                'district' => 'Blantyre',
                'physical_address' => 'Limbe, Blantyre',
                'contact_phone' => '+265 999 500 600',
                'contact_email' => 'blantyre@srhr.test',
                'service_hours' => 'Mon-Sat 08:00-16:00',
                'summary' => 'A referral-oriented access point for crisis support and reproductive health services.',
                'services' => 'Emergency referral, contraception counselling, adolescent health services',
                'is_featured' => false,
            ],
        ];

        foreach ($services as $serviceData) {
            ServiceCenter::query()->updateOrCreate(
                ['website_id' => $website->id, 'slug' => str($serviceData['name'])->slug()->value()],
                [
                    'website_id' => $website->id,
                    'name' => $serviceData['name'],
                    'category_id' => $categories[$serviceData['category']]->id,
                    'district' => $serviceData['district'],
                    'physical_address' => $serviceData['physical_address'],
                    'contact_phone' => $serviceData['contact_phone'],
                    'contact_email' => $serviceData['contact_email'],
                    'service_hours' => $serviceData['service_hours'],
                    'summary' => $serviceData['summary'],
                    'services' => $serviceData['services'],
                    'audience' => 'general',
                    'visibility' => 'public',
                    'is_featured' => $serviceData['is_featured'],
                    'is_active' => true,
                    'created_by' => $admin->id,
                    'updated_by' => $admin->id,
                ],
            );
        }

        $quiz = Quiz::query()->updateOrCreate(
            ['website_id' => $website->id, 'slug' => 'srhr-basics-check-in'],
            [
                'website_id' => $website->id,
                'title' => 'SRHR Basics Check-In',
                'summary' => 'A short confidence-building quiz covering consent, contraception, and health-seeking behaviour.',
                'intro_text' => 'Use this quiz to reinforce core SRHR knowledge and direct learners to follow-up content.',
                'audience' => 'youth',
                'visibility' => 'public',
                'status' => 'published',
                'published_at' => Carbon::now(),
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
            ],
        );

        $quiz->questions()->delete();

        $quizQuestions = [
            [
                'prompt' => 'Which statement best describes consent?',
                'help_text' => 'Pick the answer that reflects mutual respect and ongoing agreement.',
                'options' => [
                    ['option_text' => 'Consent is clear, informed, and can be withdrawn at any time.', 'is_correct' => true, 'feedback' => 'Correct. Consent must be ongoing and freely given.'],
                    ['option_text' => 'Consent only matters at the beginning of a relationship.', 'is_correct' => false, 'feedback' => 'Not correct. Consent matters every time.'],
                    ['option_text' => 'Silence means yes if two people know each other.', 'is_correct' => false, 'feedback' => 'Silence is not consent.'],
                    ['option_text' => 'Consent is optional if someone has agreed before.', 'is_correct' => false, 'feedback' => 'Previous agreement does not remove the need for consent.'],
                ],
            ],
            [
                'prompt' => 'Why is early STI testing important?',
                'help_text' => 'Think about both health outcomes and access to treatment.',
                'options' => [
                    ['option_text' => 'It allows earlier treatment and reduces complications.', 'is_correct' => true, 'feedback' => 'Correct. Early testing supports faster care and better outcomes.'],
                    ['option_text' => 'It is only needed when symptoms are severe.', 'is_correct' => false, 'feedback' => 'Many infections need earlier assessment than that.'],
                    ['option_text' => 'Testing should be avoided until symptoms go away.', 'is_correct' => false, 'feedback' => 'That increases risk and delays care.'],
                    ['option_text' => 'Testing is only useful for adults.', 'is_correct' => false, 'feedback' => 'Youth-friendly testing and care are important too.'],
                ],
            ],
            [
                'prompt' => 'What should the app help a user do in a crisis?',
                'help_text' => 'Choose the best referral-oriented answer.',
                'options' => [
                    ['option_text' => 'Find trusted support, clear next steps, and relevant services quickly.', 'is_correct' => true, 'feedback' => 'Correct. Reducing friction in urgent moments is a core product goal.'],
                    ['option_text' => 'Hide all support information until the next business day.', 'is_correct' => false, 'feedback' => 'Urgent support should be discoverable.'],
                    ['option_text' => 'Only show educational articles with no contact details.', 'is_correct' => false, 'feedback' => 'Referral pathways are part of the design.'],
                    ['option_text' => 'Require the user to register before showing help options.', 'is_correct' => false, 'feedback' => 'Critical support content should remain easy to access.'],
                ],
            ],
        ];

        foreach ($quizQuestions as $questionIndex => $questionData) {
            $question = $quiz->questions()->create([
                'website_id' => $website->id,
                'prompt' => $questionData['prompt'],
                'help_text' => $questionData['help_text'],
                'question_type' => 'single_choice',
                'sort_order' => $questionIndex + 1,
                'is_active' => true,
            ]);

            foreach ($questionData['options'] as $optionIndex => $optionData) {
                $question->options()->create([
                    'website_id' => $website->id,
                    'option_text' => $optionData['option_text'],
                    'feedback' => $optionData['feedback'],
                    'is_correct' => $optionData['is_correct'],
                    'sort_order' => $optionIndex + 1,
                ]);
            }
        }

        AppSetting::seedDefaultsForWebsite($website);

        $sliderRecords = [
            [
                'title' => 'Build a beautiful online presence that grows your brand',
                'kicker' => 'Modern digital experiences',
                'caption' => 'Launch faster with a clean landing page, elegant navigation, and a polished image slider that makes your business stand out.',
                'primary_button_text' => 'Start Project',
                'primary_button_link' => '#',
                'secondary_button_text' => 'Explore Features',
                'secondary_button_link' => '#features',
                'sort_order' => 1,
                'asset' => base_path('public/seed/hero-slide-1.svg'),
            ],
            [
                'title' => 'Design that looks premium on every screen',
                'kicker' => 'Creative and responsive',
                'caption' => 'Use Tailwind CSS to create responsive layouts, dropdown menus, and eye-catching sections with minimal effort.',
                'primary_button_text' => 'View Demo',
                'primary_button_link' => '#',
                'secondary_button_text' => 'Talk to Us',
                'secondary_button_link' => '#contact',
                'sort_order' => 2,
                'asset' => base_path('public/seed/hero-slide-2.svg'),
            ],
            [
                'title' => 'Showcase your services with confidence',
                'kicker' => 'Simple. Elegant. Effective.',
                'caption' => 'Present your products, services, and value clearly with a page structure that is clean, modern, and conversion-focused.',
                'primary_button_text' => 'Get Quote',
                'primary_button_link' => '#',
                'secondary_button_text' => 'Learn More',
                'secondary_button_link' => '#features',
                'sort_order' => 3,
                'asset' => base_path('public/seed/hero-slide-3.svg'),
            ],
        ];

        foreach ($sliderRecords as $sliderData) {
            $slider = Slider::query()->updateOrCreate(
                ['website_id' => $website->id, 'slug' => str($sliderData['title'])->slug()->value()],
                [
                    'website_id' => $website->id,
                    'title' => $sliderData['title'],
                    'kicker' => $sliderData['kicker'],
                    'caption' => $sliderData['caption'],
                    'primary_button_text' => $sliderData['primary_button_text'],
                    'primary_button_link' => $sliderData['primary_button_link'],
                    'secondary_button_text' => $sliderData['secondary_button_text'],
                    'secondary_button_link' => $sliderData['secondary_button_link'],
                    'sort_order' => $sliderData['sort_order'],
                    'is_active' => true,
                    'created_by' => $admin->id,
                    'updated_by' => $admin->id,
                ],
            );

            if ($slider->getFirstMedia('slide_image') === null && is_file($sliderData['asset'])) {
                $slider->addMedia($sliderData['asset'])->preservingOriginal()->toMediaCollection('slide_image');
            }
        }

        $publicMenus = [
            [
                'slug' => 'public-primary-about',
                'name' => 'About',
                'description' => 'Platform overview and trust-focused entry points for the public site.',
                'items' => [
                    [
                        'title' => 'About the Platform',
                        'type' => 'webview_page',
                        'target_reference' => 'content:'.$contents['About the Platform']->id.','.$contents['Privacy and Trust Online']->id,
                        'icon' => 'info',
                        'sort_order' => 1,
                        'open_in_webview' => true,
                    ],
                    [
                        'title' => 'Privacy and Trust',
                        'type' => 'content',
                        'target_reference' => 'content:'.$contents['Privacy and Trust Online']->id,
                        'icon' => 'shield-check',
                        'sort_order' => 2,
                    ],
                ],
            ],
            [
                'slug' => 'public-primary-learn',
                'name' => 'Learn',
                'description' => 'Educational SRHR content and self-guided learning tools.',
                'items' => [
                    [
                        'title' => 'Body Literacy',
                        'type' => 'category',
                        'target_reference' => 'category:'.$categories['Body Literacy']->id,
                        'icon' => 'book-open',
                        'sort_order' => 1,
                    ],
                    [
                        'title' => 'Consent Guide',
                        'type' => 'content',
                        'target_reference' => 'content:'.$contents['Consent Starts With Communication']->id,
                        'icon' => 'message-square-heart',
                        'sort_order' => 2,
                    ],
                    [
                        'title' => 'Quiz Check-In',
                        'type' => 'quiz',
                        'target_reference' => 'quiz:'.$quiz->id,
                        'icon' => 'clipboard-list',
                        'sort_order' => 3,
                    ],
                ],
            ],
            [
                'slug' => 'public-primary-support',
                'name' => 'Support',
                'description' => 'Help-seeking, FAQs, services, and referral pathways.',
                'items' => [
                    [
                        'title' => 'FAQs',
                        'type' => 'faq',
                        'target_reference' => 'faq:index',
                        'icon' => 'message-circle',
                        'sort_order' => 1,
                    ],
                    [
                        'title' => 'Find Services',
                        'type' => 'service_locator',
                        'target_reference' => 'service:index',
                        'icon' => 'map-pin',
                        'sort_order' => 2,
                    ],
                    [
                        'title' => 'Get Help',
                        'type' => 'content',
                        'target_reference' => 'content:'.$contents['Where to Get Help Quickly']->id,
                        'icon' => 'life-buoy',
                        'sort_order' => 3,
                    ],
                ],
            ],
        ];

        foreach ($publicMenus as $menuData) {
            $menu = Menu::query()->updateOrCreate(
                ['website_id' => $website->id, 'slug' => $menuData['slug']],
                [
                    'website_id' => $website->id,
                    'name' => $menuData['name'],
                    'description' => $menuData['description'],
                    'location' => 'public-primary',
                    'is_active' => true,
                ],
            );

            foreach ($menuData['items'] as $item) {
                $slug = Str::slug($item['title']);
                $menuItemName = $slug !== '' ? $slug : 'item';
                $route = $item['route'] ?? '/menu-item/'.$menuItemName;

                $menu->items()->updateOrCreate(
                    ['website_id' => $website->id, 'title' => $item['title']],
                    MenuItem::normalizeForPersistence([
                        'website_id' => $website->id,
                        'type' => $item['type'],
                        'target_reference' => $item['target_reference'] ?? null,
                        'route' => $route,
                        'icon' => $item['icon'] ?? null,
                        'sort_order' => $item['sort_order'],
                        'visibility' => 'public',
                        'open_in_webview' => $item['open_in_webview'] ?? false,
                        'is_active' => true,
                    ]),
                );
            }
        }
    }
}