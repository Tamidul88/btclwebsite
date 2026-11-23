<?php

/*
|--------------------------------------------------------------------------
| Core service pages (/services/{slug})
|--------------------------------------------------------------------------
| Kept separate from config/services.php, which Laravel reserves for
| third-party service credentials.
*/

return [

    'sap' => [
        'name' => 'SAP',
        'hero_title' => 'The Pulse of Your Business Technology',
        'hero_subtitle' => 'Building a resilient business foundation with SAP solutions',
        'accent' => '#0FAAFF',
        'hero_image' => 'images/hero/sap.jpg',
        'photo' => 'images/services/sap-team.jpg',
        'intro_title' => 'Unlock the value of SAP with Berger Tech',
        'intro' => [
            'Enterprises are investing in large-scale transformations to stay competitive in the ever-changing business environment. Businesses today need a smart ERP system that can easily embrace the end-to-end transformation of the business process while ensuring business agility and resilience to derive value from these investments. SAP solutions can help your business stay one step ahead in this direction.',
            'As a SAP Technology Partner, Berger Tech can help you plan and deploy SAP solutions to innovate, grow, and better serve your customers. For more than 10 years, our dedicated SAP Team has expertise not only on SAP but also have industry expertise which have helped number of enterprises simplify their IT landscapes and achieve reliable operations.',
            'We help you seamlessly migrate from legacy systems to a robust SAP ecosystem that can easily integrate key business components like next-gen technologies, workforce, operations, and data. We start by creating a comprehensive SAP implementation roadmap that is supported by industry-specific best practices to create the fastest path to address our client\'s business challenges.',
        ],
        'services' => [
            [
                'title' => 'SAP Advisory',
                'text' => 'SAP Advisory Services provide strategic guidance and consulting to organizations aiming to accelerate your digital transformation.',
                'details' => [
                    'Enterprise Strategy: crafting a transformation roadmap for cloud or on-prem deployment options, technology assessments, and change management.',
                    'SAP S/4HANA Roadmap: planning a risk-mitigated migration, including process discovery, impact analysis, and deployment planning.',
                    'Business process driven consulting that aligns technology with your workflows, pain points and strategic goals.',
                    'Effective harmonization and standardization of SAP solutions across business units and regions.',
                    'Analysis and optimization of existing business processes executed in SAP systems.',
                ],
            ],
            [
                'title' => 'Implementation Rollout',
                'text' => 'Implementation rollout ensures smooth integration of new technologies and processes, enhancing operational efficiency and competitiveness.',
                'details' => [
                    'Template based global rollouts with local statutory and tax localisation.',
                    'Fit-to-standard workshops, configuration, data migration and cutover planning.',
                    'Integration testing, user acceptance testing and hypercare support.',
                    'Structured change management and end-user enablement.',
                ],
            ],
            [
                'title' => 'Application Maintenance & Support',
                'text' => 'SAP Application Maintenance & Support encompasses a set of processes and methodologies that assist organizations in managing their SAP environments throughout their lifecycle.',
                'details' => [
                    'L1/L2/L3 support with defined SLAs and a single point of accountability.',
                    'Proactive system monitoring, performance tuning and housekeeping.',
                    'Enhancement backlog management and release governance.',
                    'Continuous improvement driven by incident trend analysis.',
                ],
            ],
            [
                'title' => 'SAP Migrations/Upgrades',
                'text' => 'SAP migration involves moving SAP systems between environments, while an upgrade entails updating to newer versions for enhanced features, both crucial for optimizing performance and ensuring business continuity.',
                'details' => [
                    'ECC to S/4HANA conversion: greenfield, brownfield or selective data transition.',
                    'On-premise to cloud migration on AWS and Azure with minimal downtime.',
                    'Database migration to SAP HANA including custom code remediation.',
                    'Post-migration validation, regression testing and performance benchmarking.',
                ],
            ],
        ],
        'why' => [
            'Berger Tech ensures fast and easy deployments of SAP solutions with a vast pool of experienced SAP functional and technical experts.',
            'Our in-house SAP team manages the entire SAP lifecycle and rollouts.',
            'SAP consultants with more than a decade of experience and multiple transformation projects behind them.',
        ],
    ],

    'microsoft' => [
        'name' => 'Microsoft',
        'hero_title' => 'Leveraging Emerging Technologies for Success',
        'hero_subtitle' => 'Microsoft Dynamics 365 and the modern workplace, delivered by a Microsoft Partner',
        'accent' => '#F25022',
        'hero_image' => 'images/hero/microsoft.jpg',
        'photo' => 'images/partners/microsoft-banner.png',
        'intro_title' => 'Do more with Microsoft and Berger Tech',
        'intro' => [
            'As a Microsoft Partner, Berger Tech helps organizations get the most out of the Microsoft stack, from Dynamics 365 business applications to Microsoft 365 collaboration and the Power Platform.',
            'Our consultants combine deep functional knowledge with industry experience to deliver solutions that shorten time to value and keep the total cost of ownership predictable.',
        ],
        'services' => [
            [
                'title' => 'Dynamics Migration, Upgrades & Enhancement',
                'text' => 'Move from legacy Dynamics NAV, AX or GP to Dynamics 365 and keep your platform current.',
                'details' => [
                    'Assessment of the existing landscape, customisations and integrations.',
                    'Data migration, extension re-development and cutover planning.',
                    'Continuous upgrade readiness for the Microsoft release cadence.',
                ],
            ],
            [
                'title' => 'Dynamics Implementation & Rollout',
                'text' => 'End to end implementation of Dynamics 365 Finance, Supply Chain, Sales and Business Central.',
                'details' => [
                    'Business process design aligned to Dynamics 365 best practice.',
                    'Configuration, extension development and third-party integration.',
                    'Multi-entity, multi-currency and multi-country rollouts.',
                ],
            ],
            [
                'title' => 'Maintenance Service & Support',
                'text' => 'Keep Dynamics running with a dedicated support desk and a managed enhancement pipeline.',
                'details' => [
                    'SLA-backed incident and service request management.',
                    'Environment, release and Lifecycle Services management.',
                    'Ongoing user training and adoption support.',
                ],
            ],
            [
                'title' => 'Power Platform & Modern Workplace',
                'text' => 'Automate work with Power Apps, Power Automate, Power BI and SharePoint.',
                'details' => [
                    'Low-code applications for departmental and field processes.',
                    'Workflow automation and approvals across Microsoft 365.',
                    'Analytics and dashboards on Power BI.',
                ],
            ],
        ],
        'why' => [
            'A Microsoft Partner with certified functional and technical consultants.',
            'Delivery experience across manufacturing, distribution, retail and services.',
            'One partner for ERP, collaboration, analytics and automation.',
        ],
    ],

    'cloud' => [
        'name' => 'Cloud',
        'hero_title' => 'Seamless Cloud Journey',
        'hero_subtitle' => 'Flexibility, scalability and reliability on AWS and Azure',
        'accent' => '#00B7C3',
        'hero_image' => 'images/hero/cloud.jpg',
        'photo' => 'images/services/cloud-team.jpg',
        'intro_title' => 'Cloud as a Service',
        'intro' => [
            'A cloud journey is more than a lift and shift. Berger Tech helps you build the landing zone, migrate the workload, and operate it with the right balance of cost, performance and security.',
            'From data centre services and disaster recovery collocation in our 3T data centre, to a fully managed public cloud estate, we design the model that fits your business.',
        ],
        'services' => [
            [
                'title' => 'AWS Cloud',
                'text' => 'Landing zone design, migration and managed operations on Amazon Web Services.',
                'details' => [
                    'Well-Architected landing zone, networking and identity design.',
                    'SAP and enterprise workload migration to AWS.',
                    'Cost optimisation, backup, and disaster recovery.',
                ],
            ],
            [
                'title' => 'Azure Cloud',
                'text' => 'Enterprise scale Azure adoption aligned to the Cloud Adoption Framework.',
                'details' => [
                    'Subscription, governance and policy design.',
                    'Migration of on-premise servers, databases and Dynamics workloads.',
                    'Azure monitoring, security and FinOps.',
                ],
            ],
            [
                'title' => 'Infrastructure as a Service',
                'text' => 'Data centre services for any application, including disaster recovery collocation.',
                'details' => [
                    'Data Center Service for any application.',
                    'Collocating your Disaster Recovery site in our 3T Data Center.',
                    'Active Directory components and system integration.',
                    'Infrastructure design and deployment.',
                ],
            ],
            [
                'title' => 'On Premise to Cloud Migration',
                'text' => 'A structured migration path with minimal disruption to the business.',
                'details' => [
                    'Discovery, dependency mapping and migration wave planning.',
                    'Pilot migration, validation and production cutover.',
                    'Hypercare and steady-state handover.',
                ],
            ],
        ],
        'why' => [
            'Certified cloud architects across AWS and Azure.',
            'Experience migrating mission critical SAP and Dynamics workloads.',
            'Our own 3T data centre for hybrid and disaster recovery scenarios.',
        ],
    ],

    'dynamics-365-business-central' => [
        'name' => 'Dynamics 365 Business Central',
        'hero_title' => 'Empowering Progress through Technological Advancement',
        'hero_subtitle' => 'One connected business management solution for growing companies',
        'accent' => '#7A5AF8',
        'hero_image' => 'images/hero/dynamics.jpg',
        'photo' => 'images/partners/dynamics-365.png',
        'intro_title' => 'Run your entire business with Business Central',
        'intro' => [
            'Microsoft Dynamics 365 Business Central connects finance, sales, service, and operations in a single application, so teams work from one set of numbers instead of many spreadsheets.',
            'Berger Tech implements Business Central with an industry template that shortens the project, and supports it afterwards so the platform keeps pace with the business.',
        ],
        'services' => [
            [
                'title' => 'Financial Management',
                'text' => 'General ledger, receivables, payables, fixed assets and consolidated reporting.',
                'details' => [
                    'Multi-company, multi-currency and dimension based reporting.',
                    'Bank reconciliation and cash flow forecasting.',
                    'Statutory and VAT compliance for Bangladesh.',
                ],
            ],
            [
                'title' => 'Supply Chain & Inventory',
                'text' => 'Purchasing, warehousing, and inventory control with real-time stock visibility.',
                'details' => [
                    'Requisition, purchase order and vendor management.',
                    'Warehouse receiving, put-away, pick and ship.',
                    'Item tracking with lot and serial numbers.',
                ],
            ],
            [
                'title' => 'Sales & Service',
                'text' => 'Quote to cash with the customer history visible to everyone who needs it.',
                'details' => [
                    'Quotes, orders, shipments and invoicing.',
                    'Service contracts, jobs and resource planning.',
                    'Integration with Dynamics 365 Sales and Outlook.',
                ],
            ],
            [
                'title' => 'Reporting & Analytics',
                'text' => 'Built-in analytics with Power BI dashboards for every function.',
                'details' => [
                    'Standard and custom financial reports.',
                    'Power BI datasets and embedded dashboards.',
                    'KPI monitoring and exception alerts.',
                ],
            ],
        ],
        'why' => [
            'A pre-configured industry template that reduces implementation time.',
            'Certified Business Central consultants and developers in-house.',
            'Support and enhancement services after go-live.',
        ],
    ],
];
