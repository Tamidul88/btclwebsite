<?php

/*
|--------------------------------------------------------------------------
| Insights
|--------------------------------------------------------------------------
| Long-form guides shown at /insights. Keyed by URL slug. Each section has a
| heading, one or more paragraphs and an optional bullet list; the reading
| time is worked out from the word count by App\InsightLibrary.
*/

return [

    'digital-transformation-roadmap' => [
        'title' => 'The roadmap to digital transformation',
        'category' => 'Strategy',
        'excerpt' => 'Digital transformation succeeds when it starts from business outcomes, not technology. A practical, step-by-step roadmap for leaders planning their next move.',
        'image' => 'images/features/roadmap.webp',
        'takeaways' => [
            'Start from the outcomes the business needs, then choose the technology.',
            'Map and simplify processes before you automate them.',
            'Deliver in phases, with measurable value at every step.',
            'Plan for people and data as carefully as for systems.',
        ],
        'sections' => [
            [
                'heading' => 'Start with outcomes, not technology',
                'paragraphs' => [
                    'Transformation programs stall when they begin with a product decision. Begin instead with the handful of outcomes the business needs over the next two to three years — a faster order-to-cash cycle, real-time visibility of inventory, a lower cost to serve or a better customer experience.',
                    'Agree how each outcome will be measured before any solution is chosen. Clear measures keep the program focused and make trade-offs easier when priorities compete.',
                ],
            ],
            [
                'heading' => 'Understand where you are today',
                'paragraphs' => [
                    'Document your core processes end to end, the systems that support them, and every place where data is re-keyed, reconciled or kept in spreadsheets. Those gaps are usually where the biggest wins are.',
                ],
                'points' => [
                    'Which processes depend on manual hand-offs or duplicate data entry?',
                    'Which systems are near the end of support, or costly to maintain?',
                    'Where do leaders lack timely, trusted data to make decisions?',
                ],
            ],
            [
                'heading' => 'Prioritize with a simple value-versus-effort view',
                'paragraphs' => [
                    'Score each opportunity on the business value it brings and on the effort and risk it carries. Quick wins build momentum and help fund the program; foundational work — such as a modern ERP or a clean data platform — unlocks the larger gains that follow.',
                ],
            ],
            [
                'heading' => 'Choose platforms that fit, not platforms that impress',
                'paragraphs' => [
                    'A technology-agnostic assessment compares options against your processes, your scale, your existing investments and the skills you have in house. For many organizations the right answer combines a core ERP such as SAP or Microsoft Dynamics 365 with cloud infrastructure and targeted automation.',
                ],
            ],
            [
                'heading' => 'Deliver in phases',
                'paragraphs' => [
                    'Break the roadmap into releases of a few months each, and make sure every release delivers something the business can use. Phased delivery reduces risk, surfaces issues early and keeps stakeholders engaged.',
                ],
                'points' => [
                    'Pilot with one business unit or process before scaling up.',
                    'Fix data quality before migration, not after go-live.',
                    'Build in time to test with real users and real data.',
                ],
            ],
            [
                'heading' => 'Invest in people and change',
                'paragraphs' => [
                    'New systems change how people work. Involve process owners from the start, train users in the context of their daily tasks and provide support after go-live. Adoption, not installation, is what delivers the return.',
                ],
            ],
            [
                'heading' => 'Measure, learn and iterate',
                'paragraphs' => [
                    'Track the outcome measures you agreed at the start and review them after each release. Transformation is not a single project but a capability: the organizations that do it well keep improving long after the first go-live.',
                ],
            ],
        ],
    ],

    'sap-to-cloud-migration' => [
        'title' => 'Migrating SAP workloads to the cloud',
        'category' => 'Cloud',
        'excerpt' => 'Moving SAP to AWS or Azure can improve resilience, scalability and cost control. How to plan the migration and avoid the most common pitfalls.',
        'image' => 'images/features/sap-to-cloud.webp',
        'takeaways' => [
            'Assess the full SAP landscape, its integrations and its data volumes first.',
            'Pick the migration approach that matches your timeline and appetite for change.',
            'Design security, backup and disaster recovery in from day one.',
            'Rehearse the cutover until downtime is predictable.',
        ],
        'sections' => [
            [
                'heading' => 'Why move SAP to the cloud',
                'paragraphs' => [
                    'On-premise SAP landscapes tie up capital in hardware that has to be sized for peak load and refreshed every few years. Running SAP on a hyperscaler such as AWS or Microsoft Azure turns that into an operating cost, lets you add capacity when you need it, and makes high availability and disaster recovery far easier to achieve.',
                    'It is also a natural step on the road to SAP S/4HANA. SAP has set the end of 2027 for mainstream maintenance of SAP ECC 6.0, so many organizations are planning their move to the cloud and their S/4HANA transition together.',
                ],
            ],
            [
                'heading' => 'Assess the current landscape',
                'paragraphs' => [
                    'A successful migration starts with a complete picture of what you run today.',
                ],
                'points' => [
                    'Inventory every SAP system, environment (development, quality, production) and version.',
                    'Map the interfaces to banks, tax authorities, suppliers and other internal systems.',
                    'Measure database size and growth, and archive data you no longer need.',
                    'Record current performance and availability, so you can prove the improvement.',
                ],
            ],
            [
                'heading' => 'Choose a migration approach',
                'paragraphs' => [
                    'There is no single right path. The approach depends on how quickly you need to move and how much change the business can absorb at once.',
                ],
                'points' => [
                    'Rehost (lift and shift): move systems as they are onto cloud infrastructure. Fastest, with the least change for users.',
                    'Replatform: move and modernize the technical layer at the same time — for example, migrating the database to SAP HANA.',
                    'Transform: combine the move with a conversion to, or re-implementation on, SAP S/4HANA to redesign processes.',
                ],
            ],
            [
                'heading' => 'Design for security and resilience',
                'paragraphs' => [
                    'Plan network connectivity, identity and access, encryption, backup and disaster recovery before the first system moves. Cloud regions and availability zones make it possible to protect production SAP against the failure of a single data center — but only if the architecture is designed for it.',
                ],
            ],
            [
                'heading' => 'Test, rehearse and cut over',
                'paragraphs' => [
                    'Run at least one full mock migration with production-sized data. Rehearsals show how long the real cutover will take, expose data and interface issues early, and give the business confidence in the go-live plan.',
                ],
                'points' => [
                    'Agree the downtime window with business owners well in advance.',
                    'Test interfaces, printing and reports — not just transactions.',
                    'Prepare, and test, a rollback plan.',
                ],
            ],
            [
                'heading' => 'Optimize after go-live',
                'paragraphs' => [
                    'Once you have moved, right-size instances based on real usage, shut non-production systems down out of hours and review costs every month. A period of hypercare support resolves issues quickly while users settle in.',
                ],
            ],
        ],
    ],

    'choosing-sap-or-dynamics-365' => [
        'title' => 'SAP or Microsoft Dynamics 365: choosing the right ERP',
        'category' => 'ERP',
        'excerpt' => 'Both are leading ERP platforms, but they suit different organizations. The questions to ask before you choose.',
        'image' => 'images/hero/dynamics.jpg',
        'takeaways' => [
            'Choose on fit with your processes, your scale and your growth plans.',
            'Take account of the systems and skills you already have.',
            'Compare the total cost of ownership, not just licenses.',
            'The implementation partner matters as much as the product.',
        ],
        'sections' => [
            [
                'heading' => 'Two strong options',
                'paragraphs' => [
                    'SAP and Microsoft Dynamics 365 are both proven, widely used ERP platforms. SAP S/4HANA is often chosen by large, complex organizations with demanding manufacturing, supply chain and finance requirements. Microsoft Dynamics 365 — including Business Central for small and mid-sized businesses — is often chosen for its flexibility, speed of deployment and close integration with Microsoft 365.',
                    'Neither is the right answer for everyone. The best choice is the one that fits how your business works today and how it plans to grow.',
                ],
            ],
            [
                'heading' => 'Start with your processes',
                'paragraphs' => [
                    'List the processes that make your business different — how you plan production, set prices, distribute or serve customers — and test each platform against them. Standard functionality that covers these processes well means fewer customizations, lower cost and easier upgrades.',
                ],
            ],
            [
                'heading' => 'Consider scale and complexity',
                'paragraphs' => [
                    'Look ahead five years, not just at today.',
                ],
                'points' => [
                    'The number of legal entities, countries and currencies.',
                    'The volume of transactions and users, now and as you grow.',
                    'Industry requirements such as batch traceability or complex manufacturing.',
                    'Reporting and consolidation needs at group level.',
                ],
            ],
            [
                'heading' => 'Look at your existing ecosystem',
                'paragraphs' => [
                    'If your organization already runs on Microsoft 365, Teams and Power BI, Dynamics 365 fits naturally alongside those tools. If parts of your group or your supply chain partners already use SAP, standardizing on SAP can simplify integration and reporting.',
                ],
            ],
            [
                'heading' => 'Weigh the total cost of ownership',
                'paragraphs' => [
                    'Licenses are only part of the cost. Include implementation, data migration, integration, infrastructure or cloud subscriptions, training, support and future upgrades. A five-year view gives a fairer comparison than the first-year price.',
                ],
            ],
            [
                'heading' => 'Choose the partner as carefully as the product',
                'paragraphs' => [
                    'Successful ERP projects depend on the team that delivers them. Look for certified consultants, relevant industry experience, a clear methodology and support after go-live. An independent assessment before you commit can save significant time and cost.',
                ],
            ],
        ],
    ],

    'getting-started-with-automation' => [
        'title' => 'Where to start with business process automation',
        'category' => 'Automation',
        'excerpt' => 'Robotic process automation (RPA) can take repetitive work off your team in weeks, not months. How to pick the right first processes and scale with confidence.',
        'image' => 'images/hero/cloud.jpg',
        'takeaways' => [
            'Automate stable, rule-based, high-volume tasks first.',
            'Fix and standardize a process before you automate it.',
            'Measure hours saved, error rates and cycle time from day one.',
            'Set up governance early so automation scales safely.',
        ],
        'sections' => [
            [
                'heading' => 'What RPA does well',
                'paragraphs' => [
                    'Robotic process automation uses software robots to perform repetitive, rule-based tasks across your existing applications — the same clicks and keystrokes a person would make. Because it works without changing the underlying systems, it is one of the fastest ways to reduce manual effort.',
                    'Typical examples include entering invoices, reconciling bank statements, updating records across systems and preparing routine reports.',
                ],
            ],
            [
                'heading' => 'Pick the right first processes',
                'paragraphs' => [
                    'The best candidates share four traits:',
                ],
                'points' => [
                    'Rule-based: clear decisions with few exceptions.',
                    'High volume: done often enough for the savings to add up.',
                    'Stable: the process, and the screens it uses, rarely change.',
                    'Digital inputs: data arrives in a structured, electronic form.',
                ],
            ],
            [
                'heading' => 'Fix the process before automating it',
                'paragraphs' => [
                    'Automating a broken process only makes mistakes faster. Map the process, remove unnecessary steps and standardize how exceptions are handled before you build a robot. Sometimes this review shows that a configuration change in your ERP is a better fix than automation.',
                ],
            ],
            [
                'heading' => 'Prove the value with a pilot',
                'paragraphs' => [
                    'Start with one or two processes and agree the measures up front: hours saved, error rates, cycle time and staff satisfaction. A successful pilot builds both the business case and the internal support to go further.',
                ],
            ],
            [
                'heading' => 'Scale with governance',
                'paragraphs' => [
                    'As the number of robots grows, set clear ownership, change control, credential management and monitoring. A small center of excellence that prioritizes requests and maintains standards keeps automation reliable and secure.',
                ],
            ],
            [
                'heading' => 'Look beyond RPA',
                'paragraphs' => [
                    'RPA is one tool among several. Workflow on SharePoint, integration between systems, IIoT for capturing data from machines, and analytics all play a part. The best results come from choosing the right tool for each step of the process.',
                ],
            ],
        ],
    ],

];
