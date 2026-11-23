<?php

/*
|--------------------------------------------------------------------------
| Solution pages (/solutions/{slug})
|--------------------------------------------------------------------------
| 'card' drives the Our Solutions grid on the home page; the remaining keys
| drive the detail page.
*/

return [

    'smart-delivery' => [
        'name' => 'Smart Delivery',
        'card' => 'This technology-powered delivery solution offers organizations to be smart with its features and functionalities.',
        'hero_gradient' => ['#1f2750', '#5B21B6'],
        'card_image' => 'images/solutions/smart-delivery.jpg',
        'hero_image' => 'images/solutions/smart-delivery-hero.webp',
        'summary' => 'This technology-powered delivery solution offers organizations to be smart with its features and functionalities. Track every trip in real time, keep customers informed automatically, and close the loop between the sales office, the dealer and the plant.',
        'sections' => [
            [
                'title' => 'Key Features',
                'style' => 'grid',
                'items' => [
                    'Real time tracking',
                    'Historical reports and dashboard summary',
                    'SMS notification to customers informing current delivery status',
                    'App for delivery personnel as well as distribution concerns',
                    'Trip history',
                    'Anytime anywhere access',
                    'Unloading and delay time calculation',
                    'Geo fencing as per office location and dealer location',
                    'Easy to use, user friendly interface',
                ],
            ],
            [
                'title' => 'Other Features',
                'style' => 'list',
                'items' => [
                    'Route wise / vehicle wise distance covered',
                    'Route wise / customer wise / vehicle wise delay',
                    'Route wise / customer wise trip count',
                    'Route wise / customer wise unloading cycle',
                    'Customer wise distance covered',
                    'Route wise / customer wise transit cycle time',
                ],
            ],
            [
                'title' => 'Solution Applicable For',
                'style' => 'steps',
                'items' => [
                    'Delivery from the sales office to dealer',
                    'Delivery from sales office to distributor',
                    'Delivery from factory to sales office',
                    'Inter plant product transfer',
                ],
            ],
        ],
        'benefits' => [
            'Lower transport cost through measurable route efficiency',
            'Fewer delivery disputes with a verifiable trip record',
            'Better customer experience with proactive status updates',
        ],
    ],

    'logistic-automation' => [
        'name' => 'Logistic Automation',
        'card' => 'Complete IIOT solution for Supply Chain for automating logistic operation.',
        'hero_gradient' => ['#0F766E', '#1f2750'],
        'card_image' => 'images/solutions/logistic-automation.jpg',
        'hero_image' => 'images/solutions/logistic-automation-hero.jpg',
        'summary' => 'A complete IIOT solution for the supply chain that automates the logistic operation end to end, from delivery scheduling and gate entry through weighbridge capture to ERP posting.',
        'sections' => [
            [
                'title' => 'Key Features',
                'style' => 'grid',
                'items' => [
                    'Delivery scheduling',
                    'Real time transport tracking',
                    'Inter location transport management',
                    'Gate entry and weighbridge integration',
                    'Rich analytics',
                    'QR code printer and reader, surveillance camera integration',
                    'ERP integration',
                ],
            ],
            [
                'title' => 'How It Works',
                'style' => 'steps',
                'items' => [
                    'Schedule the delivery and assign the vehicle',
                    'Capture gate entry with QR code and camera',
                    'Record gross and tare weight from the weighbridge',
                    'Track the vehicle in transit until proof of delivery',
                    'Post the transaction back into the ERP',
                ],
            ],
        ],
        'benefits' => [
            'Removes manual gate registers and paper weighbridge slips',
            'Cuts vehicle turnaround time at the plant',
            'Gives management one analytics view across every location',
        ],
    ],

    'procure-to-pay' => [
        'name' => 'Procure To Pay',
        'card' => 'It is integrating purchasing and accounts payable systems to create greater efficiencies.',
        'hero_gradient' => ['#1f2750', '#1E3A8A'],
        'card_image' => 'images/solutions/procure-to-pay.jpg',
        'hero_image' => 'images/solutions/procure-to-pay-hero.webp',
        'summary' => 'Procure to Pay integrates purchasing and accounts payable systems to create greater efficiencies. It exists within the larger procurement management process and involves four key stages: selecting goods and services; enforcing compliance and order; receiving and reconciliation; invoicing and payment.',
        'sections' => [
            [
                'title' => 'Process Flow',
                'style' => 'steps',
                'items' => [
                    'Selecting goods and services',
                    'Enforcing compliance and order',
                    'Receiving and reconciliation',
                    'Invoicing and payment',
                ],
            ],
            [
                'title' => 'Features and Functionality',
                'style' => 'grid',
                'items' => [
                    'RFQ preparation from PR and floating to the vendor',
                    'Quotation and bill submission from vendor through online',
                    'End to end integration with ERP (PR to financial posting)',
                    'Quotation comparison (commercial and technical) and approval process',
                    'E-bidding / auction facility',
                    'Data security, audit log, authorization, email notification',
                    'Track the documents from end to end',
                    'Analytics dashboard',
                ],
            ],
        ],
        'benefits' => [
            'Shorter procurement cycle time from requisition to payment',
            'Transparent vendor comparison with a full audit trail',
            'No re-keying between the procurement portal and the ERP',
        ],
    ],

    'sharepoint-solution' => [
        'name' => 'SharePoint Solution',
        'card' => 'SharePoint is a highly flexible, responsive, and customizable platform that enterprises prefer to use when it comes to content management, collaboration, workflow creation, and Business Process Automation.',
        'hero_gradient' => ['#1D4ED8', '#0F172A'],
        'card_image' => 'images/solutions/sharepoint-solution.jpg',
        'hero_image' => 'images/solutions/sharepoint-solution-hero.jpg',
        'summary' => 'SharePoint is a highly flexible, responsive, and customizable platform that enterprises prefer to use when it comes to content management, collaboration, workflow creation, and Business Process Automation. Using this highly configurable feature, several Business Process Automation ready solutions are available which can help organizations become paperless and increase productivity.',
        'sections' => [
            [
                'title' => 'Key Features',
                'style' => 'grid',
                'items' => [
                    'Faster adoption of process',
                    'Device independent access from anywhere anytime',
                    'Paperless office',
                    'Data security and integrity',
                ],
            ],
            [
                'title' => 'Business Benefits',
                'style' => 'list',
                'items' => [
                    'Simple sharing and collaboration platform',
                    'Transform business processes',
                    'Document archival and file storage',
                    'Natively integrate with Microsoft Office and products',
                    'Engage and inform your organization',
                    'Analytics and intelligence capability',
                    'IT service management',
                    'HRIS solution',
                    'Financial processes',
                    'Marketing and event management',
                    'Supply chain processes',
                ],
            ],
            [
                'title' => 'Berger Tech Ready Solutions',
                'style' => 'grid',
                'items' => [
                    'Pick & Drop Vehicle Management',
                    'Customer / Dealer App',
                    'Consumer\'s Application',
                ],
            ],
        ],
        'benefits' => [
            'Ready solutions shorten the path from idea to running process',
            'One platform for documents, workflow and reporting',
            'Native fit with the Microsoft 365 licences you already own',
        ],
    ],

    'salesforce-automation' => [
        'name' => 'Salesforce Automation',
        'card' => 'Empowers the Sales team and automates sales processes to deliver the highest customer experience. Improve your customer experience through one-stop Customer Connect application to address all their needs. Better order fulfillment rate.',
        'hero_gradient' => ['#7C3AED', '#1f2750'],
        'card_image' => 'images/solutions/salesforce-automation.jpg',
        'hero_image' => 'images/solutions/salesforce-automation-hero.jpg',
        'summary' => 'Empowers the sales team and automates sales processes to deliver the highest customer experience. Improve your customer experience through a one-stop Customer Connect application that addresses all their needs, and lift your order fulfillment rate.',
        'sections' => [
            [
                'title' => 'Key Features',
                'style' => 'grid',
                'items' => [
                    'Sales organogram, attendance',
                    'Route plan and KPI',
                    'Geo-location',
                    'Lead management',
                    'Secondary sales',
                    'Complaint management',
                    'ERP / SAP integrated order taking',
                    'Retailer, merchandizing',
                ],
            ],
            [
                'title' => 'Customer Connect',
                'style' => 'list',
                'items' => [
                    'One application for orders, complaints and account status',
                    'Real time stock and price visibility for the field force',
                    'Attendance and journey plan compliance on the same app',
                ],
            ],
        ],
        'benefits' => [
            'Higher field force productivity against the journey plan',
            'Orders reach the ERP the moment they are taken',
            'Complaints are tracked to closure instead of over the phone',
        ],
    ],
];
