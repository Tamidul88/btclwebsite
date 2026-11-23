<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Company
    |--------------------------------------------------------------------------
    */

    'name' => 'Berger Tech Consulting Limited',
    'short_name' => 'Berger Tech',
    'tagline' => 'A subsidiary of Berger Paints Bangladesh Limited',
    'founded' => 2022,

    /*
    | Both logo files are white artwork on transparency, so they are only ever
    | placed on the blue header or the dark footer.
    */
    'logo' => 'images/logo/berger-tech-wordmark.png',
    'logo_footer' => 'images/logo/berger-tech-logo.png',

    'contact' => [
        'address_lines' => [
            'Berger House, House 8, Road 2',
            'Sector 3, Uttara Model Town',
            'Dhaka 1230',
        ],
        'phone' => '02-48953665-70',
        'phone_link' => '+8800248953665',
        'email' => 'info@bergertechbd.com',
        'map' => 'https://maps.google.com/?q=Berger+House+Uttara+Model+Town+Dhaka',
    ],

    /*
    |--------------------------------------------------------------------------
    | Consultation booking
    |--------------------------------------------------------------------------
    | Drives the slot picker on the appointment page. Days use Carbon's
    | numbering (0 = Sunday), and times are local to the timezone below.
    */

    'booking' => [
        'title' => 'Discuss Your Project',
        'timezone' => 'Asia/Dhaka',
        'timezone_label' => 'Bangladesh Time (GMT+6)',
        'duration' => 30,
        'days' => [0, 1, 2, 3, 4],
        'opens' => '09:00',
        'closes' => '18:00',
        'min_notice_hours' => 4,
        'days_ahead' => 60,
    ],

    'social' => [
        ['name' => 'Linkedin', 'icon' => 'linkedin', 'url' => 'https://www.linkedin.com/'],
        ['name' => 'Facebook', 'icon' => 'facebook', 'url' => 'https://www.facebook.com/'],
        ['name' => 'Youtube', 'icon' => 'youtube', 'url' => 'https://www.youtube.com/'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Mega menu — Our Services
    |--------------------------------------------------------------------------
    | Each group renders as a column block inside the full screen menu.
    */

    'menu_services' => [
        [
            'title' => 'ERP as a Service',
            'icon' => 'layers',
            'summary' => 'Advisory, implementation, migration, hosting and support for SAP and Microsoft Dynamics 365.',
            'groups' => [
                [
                    'label' => 'SAP',
                    'route' => 'services.show',
                    'slug' => 'sap',
                    'items' => [
                        'SAP Advisory',
                        'SAP Cloud Hosting',
                        'SAP Migration, Upgrades and Enhancement',
                        'SAP Implementation and Rollout',
                        'SAP Application Maintenance Service and Support',
                    ],
                ],
                [
                    'label' => 'Microsoft Dynamics 365',
                    'route' => 'services.show',
                    'slug' => 'microsoft',
                    'items' => [
                        'Microsoft Dynamics Migration, Upgrades and Enhancement',
                        'Microsoft Dynamics Implementation and Rollout',
                        'Microsoft Dynamics Maintenance Service and Support',
                    ],
                ],
            ],
        ],
        [
            'title' => 'Development as a Service',
            'icon' => 'code',
            'summary' => 'Web and mobile applications, SharePoint business processes and analytics.',
            'groups' => [
                [
                    'label' => null,
                    'items' => [
                        'Business Process on SharePoint',
                        'Mobile Application Development',
                        'Web Application Development',
                        'Analytics',
                    ],
                ],
            ],
        ],
        [
            'title' => 'Automation as a Service',
            'icon' => 'cpu',
            'summary' => 'RPA, manufacturing automation, IIoT and blockchain to remove manual work.',
            'groups' => [
                [
                    'label' => null,
                    'items' => ['Blockchain', 'Manufacturing Automation', 'RPA', 'IIOT'],
                ],
            ],
        ],
        [
            'title' => 'Security as a Service',
            'icon' => 'shield',
            'summary' => 'SOC, SIEM, penetration testing and security consultancy — including SAP security.',
            'groups' => [
                [
                    'label' => null,
                    'items' => [
                        'SOC Service',
                        'Security Implementation',
                        'Vulnerability Assessment and Penetration Testing',
                        'Security Consultancy',
                        'SEIM Implementation',
                        'SAP Security',
                    ],
                ],
            ],
        ],
        [
            'title' => 'Audit as a Service',
            'icon' => 'clipboard',
            'summary' => 'Independent IT and SAP audits to check controls, risk and compliance.',
            'groups' => [
                ['label' => null, 'items' => ['IT Audit', 'SAP Audit']],
            ],
        ],
        [
            'title' => 'Infrastructure as a Service',
            'icon' => 'server',
            'summary' => 'Data center services, disaster recovery, system integration and cloud migration.',
            'groups' => [
                [
                    'label' => null,
                    'items' => [
                        'Data Center Service for any Application',
                        'Collocating your Disaster Recovery site in our 3T Data Center',
                        'Active Directory Components',
                        'On Premise to Cloud Migration Service',
                        'Infrastructure Design and Deployment',
                        'System Integration',
                    ],
                ],
            ],
        ],
        [
            'title' => 'Cloud as a Service',
            'icon' => 'cloud',
            'summary' => 'Design, migration and operation on AWS and Azure.',
            'groups' => [
                [
                    'label' => null,
                    'route' => 'services.show',
                    'slug' => 'cloud',
                    'items' => ['AWS Cloud', 'Azure Cloud'],
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Home page content
    |--------------------------------------------------------------------------
    */

    'hero' => [
        'title' => 'Simplifying Business',
        'lines' => [
            'Simplify the complexities of running a successful enterprise so you can focus on what you do best.',
            'With our innovative solutions and expert guidance, navigating the intricacies of entrepreneurship becomes effortless.',
        ],
        'cta' => 'Set an Appointment',
        'image' => 'images/hero/home.webp',
    ],

    'service_cards' => [
        [
            'badge' => 'SAP',
            'slug' => 'sap',
            'logo' => 'images/partners/sap.png',
            'text' => 'Value realization. Future readiness. Competitive advantage.',
        ],
        [
            'badge' => 'Microsoft Partner',
            'slug' => 'microsoft',
            'logo' => 'images/partners/microsoft.png',
            'text' => 'Leveraging Emerging Technologies for Success.',
        ],
        [
            'badge' => 'Cloud',
            'slug' => 'cloud',
            'logo' => 'images/partners/cloud.png',
            'text' => 'Seamless Cloud journey with Flexibility, Scalability, and Reliability.',
        ],
        [
            'badge' => 'Dynamics 365',
            'slug' => 'dynamics-365-business-central',
            'logo' => 'images/partners/dynamics-365.png',
            'text' => 'Empowering Progress through Technological Advancement and Digital Transformation.',
        ],
    ],

    /*
    | The customer strip, in the order the source site shows it. A few logos
    | there carry no company name in the file, so they are labelled generically.
    */
    'customers' => [
        ['name' => 'City Group', 'logo' => 'images/customers/city-group.webp'],
        ['name' => 'Healthcare Pharmaceuticals', 'logo' => 'images/customers/healthcare.jpg'],
        ['name' => 'Butterfly', 'logo' => 'images/customers/butterfly.webp'],
        ['name' => 'Kona Software Lab', 'logo' => 'images/customers/kona-software-lab.webp'],
        ['name' => 'GPH Ispat', 'logo' => 'images/customers/gph-ispat.webp'],
        ['name' => 'Singer', 'logo' => 'images/customers/singer.webp'],
        ['name' => 'Bergen Engines', 'logo' => 'images/customers/bergen-engines.png'],
        ['name' => 'Air Alliance', 'logo' => 'images/customers/air-alliance.jpg'],
        ['name' => 'Heidelberg Materials', 'logo' => 'images/customers/heidelberg-materials.svg'],
        ['name' => 'Bashundhara Group', 'logo' => 'images/customers/bashundhara-group.webp'],
        ['name' => 'Buildbest', 'logo' => 'images/customers/buildbest.png'],
        ['name' => 'UK Paint', 'logo' => 'images/customers/uk-paint.webp'],
        ['name' => 'JNBL', 'logo' => 'images/customers/jnbl.jpg'],
        ['name' => 'Fosroc', 'logo' => 'images/customers/fosroc.webp'],
        ['name' => 'Beckers', 'logo' => 'images/customers/beckers.jpg'],
        ['name' => 'Client', 'logo' => 'images/customers/logo-1.webp'],
        ['name' => 'Client', 'logo' => 'images/customers/images.webp'],
        ['name' => 'Client', 'logo' => 'images/customers/images-2.jpg'],
        ['name' => 'Client', 'logo' => 'images/customers/image.png'],
        ['name' => 'Client', 'logo' => 'images/customers/logo.png'],
        ['name' => 'Client', 'logo' => 'images/customers/untitled-1.svg'],
    ],

    'features' => [
        [
            'kicker' => 'Simplifying Business',
            'title' => 'Empowering Progress Through Technological Advancements and Digital Transformation',
            'cta' => 'Our Story',
            'url_name' => 'about',
            'image' => 'images/features/simplifying-business.webp',
        ],
        [
            'kicker' => 'The Roadmap to Digital Transformation',
            'title' => 'Leveraging emerging technology for success',
            'cta' => 'Read Whitepaper',
            'insight' => 'digital-transformation-roadmap',
            'image' => 'images/features/roadmap.webp',
        ],
        [
            'kicker' => 'Migrating SAP workload to Cloud',
            'title' => 'Streamlining Cloud Migration Strategies for a Seamless Transition to the Future',
            'cta' => 'Read Whitepaper',
            'insight' => 'sap-to-cloud-migration',
            'image' => 'images/features/sap-to-cloud.webp',
        ],
        [
            'kicker' => 'Expanding our Footprint',
            'title' => 'Bringing Our Expertise Closer to You and Their Impact on Innovation and Collaboration',
            'cta' => 'Set an Appointment',
            'url_name' => 'appointment',
            'image' => 'images/features/footprint.webp',
        ],
    ],

    // "What's happening" gallery on the home page (components/whats-happening).
    // An item with an 'insight' slug takes its label, text and image from
    // config/insights.php; set any of them here to override.
    'happening' => [
        'title' => 'What’s happening',
        'highlight' => 'happening',
        'subtitle' => 'See the latest from Berger Tech.',
        'items' => [
            [
                'label' => 'Simplifying Business',
                'category' => 'Our company',
                'lead' => 'Empowering progress through technology.',
                'text' => 'Since 2022 we have helped companies speed up their digital transformation with a tech-agnostic approach and deep domain expertise in SAP, Microsoft and cloud.',
                'cta' => 'Our story',
                'url_name' => 'about',
                'image' => 'images/features/simplifying-business.webp',
            ],
            [
                'insight' => 'digital-transformation-roadmap',
                'lead' => 'Leveraging emerging technology for success.',
                'cta' => 'Read the guide',
            ],
            [
                'insight' => 'sap-to-cloud-migration',
                'lead' => 'A seamless transition to the cloud.',
                'cta' => 'Read the guide',
            ],
            [
                'insight' => 'choosing-sap-or-dynamics-365',
                'lead' => 'Two leading ERPs, one right fit.',
                'cta' => 'Read the guide',
            ],
            [
                'insight' => 'getting-started-with-automation',
                'lead' => 'Automation in weeks, not months.',
                'cta' => 'Read the guide',
            ],
            [
                'label' => 'Expanding our footprint',
                'category' => 'Growth',
                'lead' => 'Bringing our expertise closer to you.',
                'text' => 'Our consultants work alongside your teams to drive innovation and collaboration. Book a session and we will map out where to start.',
                'cta' => 'Set an appointment',
                'url_name' => 'appointment',
                'image' => 'images/features/footprint.webp',
            ],
        ],
    ],

    // Full-width photo banner on the home page (components/feature-banner).
    'banner' => [
        'title' => 'Building the digital backbone for modern business',
        'highlight' => 'digital backbone',
        'cta' => 'Discover our story',
        'url_name' => 'about',
        'image' => 'images/features/simplifying-business.webp',
    ],

    'story_image' => 'images/tech-team-day.jpg',
    'services_band_image' => 'images/patterns/services-band.jpg',
    'culture_image' => 'images/patterns/culture.webp',
    'culture_photos' => ['images/tech-team-day-group.jpg', 'images/tech-team-day-lawn.jpg'],

    'story' => [
        'Berger Tech Consulting Limited a next-generation technology solutions and services company. The company was founded in 2022 with the goal of facilitating companies to expedite their digital and technological transformation processes. Berger Tech services uses a tech-agnostic approach to support clients thrive in today\'s fast-changing and highly competitive world. In this century technology landscape is evolving rapidly, to survive technology-led transformation is essential.',
        'The combination of our technology solutions and services with our deep domain expertise can enable partners to simplify business operations, lower costs, maximize revenue, and gain a competitive advantage. Berger Tech Consulting pledges to continue to adapt effective emerging innovative technology solutions and services to transform businesses and uncover new growth opportunities for our customers.',
        'Berger Tech Consulting is a subsidiary of Berger Paints Bangladesh Limited, leading manufacturers and market leader in Bangladesh known for its diversified portfolio of paint & coating products and services. Both Berger Paints and Berger Tech foster a culture of deep trust. Trust is inviolable in all of our client relations as we work towards our vision of becoming a trusted technology solutions company.',
    ],

    'why_us' => [
        ['text' => 'Certified, Skilled and Experience Professionals', 'icon' => 'images/why/certificate.png'],
        ['text' => 'Believes on Quality rather than Quantity', 'icon' => 'images/why/high-quality.png'],
        ['text' => 'Business oriented Project Management', 'icon' => 'images/why/growth.png'],
        ['text' => 'Experience on SAP ERP and Dynamics 365 ERP', 'icon' => 'images/why/erp.png'],
        ['text' => '12+ Years\' Experience on Technological solutions', 'icon' => 'images/why/calendar.png'],
        ['text' => 'Maintain Standard and Best Practices', 'icon' => 'images/why/medal.png'],
        ['text' => 'In touch with world renowned Organizations', 'icon' => 'images/why/internet.png'],
        ['text' => 'Berger heritage of being Trusted Worldwide', 'icon' => 'images/why/teamwork.png'],
    ],

    'culture' => [
        [
            'title' => 'Accountability',
            'icon' => 'target',
            'text' => 'Accountability represents having an ownership mindset. This enables the employees to think and act like owners and take complete responsibility for the outcome of their work.',
        ],
        [
            'title' => 'Solution Mindset',
            'icon' => 'layers',
            'text' => 'Having a solution mindset is the practice of not seeing a big problem as a whole. Instead, we promote breaking it down into smaller, solvable pieces.',
        ],
        [
            'title' => 'Collaboration',
            'icon' => 'users',
            'text' => 'We believe that employee engagement is a must to embrace innovation at the grassroots. Hence, we drive collaboration across teams, functions and geographies.',
        ],
        [
            'title' => 'Expertise',
            'icon' => 'book',
            'text' => 'The expertise tenet focuses on inculcating a learning mindset in the employees. Everyone needs to upskill and aim to become an expert in their chosen field.',
        ],
        [
            'title' => 'Never Give-Up',
            'icon' => 'flag',
            'text' => 'The never-give-up tenet encourages an attitude to thrive on achieving the desired results. The focus is to help work on and embrace challenges without flinching.',
        ],
        [
            'title' => 'Dependability',
            'icon' => 'shield',
            'text' => 'Dependability is directly proportional to accountability. While doing so, the outcome is timely and complete. The last pillar holds the culture together.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Technology stack
    |--------------------------------------------------------------------------
    | Platforms named across the service catalogue, grouped for the home page.
    */

    'tech_stack' => [
        ['group' => 'ERP', 'items' => ['SAP', 'Microsoft Dynamics 365', 'Dynamics 365 Business Central']],
        ['group' => 'Cloud', 'items' => ['Microsoft Azure', 'Amazon Web Services', 'Active Directory']],
        ['group' => 'Apps & CRM', 'items' => ['SharePoint', 'Salesforce', 'Web & Mobile', 'Analytics']],
        ['group' => 'Automation', 'items' => ['RPA', 'IIoT', 'Blockchain', 'Manufacturing Automation']],
        ['group' => 'Security', 'items' => ['SOC', 'SIEM', 'VAPT', 'SAP Security']],
    ],

    /*
    |--------------------------------------------------------------------------
    | Industries
    |--------------------------------------------------------------------------
    | Grouped from the customer list above.
    */

    'industries' => [
        [
            'name' => 'Manufacturing & Materials',
            'icon' => 'factory',
            'customers' => ['GPH Ispat', 'Heidelberg Materials', 'Fosroc', 'Beckers', 'UK Paint'],
        ],
        [
            'name' => 'Conglomerates',
            'icon' => 'building',
            'customers' => ['City Group', 'Bashundhara Group'],
        ],
        [
            'name' => 'Consumer & Retail',
            'icon' => 'bag',
            'customers' => ['Singer', 'Butterfly'],
        ],
        [
            'name' => 'Pharmaceuticals',
            'icon' => 'pill',
            'customers' => ['Healthcare Pharmaceuticals'],
        ],
        [
            'name' => 'Engineering & Logistics',
            'icon' => 'truck',
            'customers' => ['Bergen Engines', 'Air Alliance'],
        ],
        [
            'name' => 'Technology',
            'icon' => 'code',
            'customers' => ['Kona Software Lab'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | How we engage
    |--------------------------------------------------------------------------
    */

    'process' => [
        [
            'title' => 'Discover',
            'text' => 'A consultation to understand your processes, systems and the outcomes the business needs.',
        ],
        [
            'title' => 'Design',
            'text' => 'A practical roadmap and solution architecture built on industry standards and best practices.',
        ],
        [
            'title' => 'Deliver',
            'text' => 'Implementation and rollout by certified consultants, with business-oriented project management.',
        ],
        [
            'title' => 'Support',
            'text' => 'Maintenance, support and continuous enhancement so the solution keeps pace with the business.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Frequently asked questions
    |--------------------------------------------------------------------------
    */

    'faq' => [
        [
            'question' => 'What services does Berger Tech offer?',
            'answer' => 'ERP as a service (SAP and Microsoft Dynamics 365), development, automation, security, audit, infrastructure and cloud — each delivered as a service, from advisory through implementation to ongoing support.',
        ],
        [
            'question' => 'Who is Berger Tech Consulting?',
            'answer' => 'A next-generation technology solutions and services company founded in 2022, and a subsidiary of Berger Paints Bangladesh Limited.',
        ],
        [
            'question' => 'Can you help us move to the cloud?',
            'answer' => 'Yes. We migrate on-premise workloads — including SAP — to AWS and Azure, and can host your disaster recovery site in our data center.',
        ],
        [
            'question' => 'Do you support systems after go-live?',
            'answer' => 'Yes. We provide application maintenance, support, upgrades and enhancements for SAP and Microsoft Dynamics 365 throughout their lifecycle.',
        ],
        [
            'question' => 'How do I book a consultation?',
            'answer' => 'Pick a 30-minute slot on our appointment page, Sunday to Thursday between 9:00 AM and 6:00 PM (GMT+6). Meetings are online or at Berger House, and we confirm by email.',
        ],
        [
            'question' => 'Where is your office?',
            'answer' => 'Berger House, House 8, Road 2, Sector 3, Uttara Model Town, Dhaka 1230.',
        ],
    ],
];
