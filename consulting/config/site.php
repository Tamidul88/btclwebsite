<?php

/*
|--------------------------------------------------------------------------
| Site content
|--------------------------------------------------------------------------
| Marketing copy lives here rather than in the database: it is versioned with
| the code, cacheable via config:cache, and carries no admin CRUD surface to
| secure. All copy below is original placeholder text for {{COMPANY}}.
*/

return [

    'company' => env('COMPANY_NAME', '{{COMPANY}}'),
    'tagline' => 'Engineering calm into complex systems',
    'founded' => 2014,

    'contact' => [
        'email' => env('COMPANY_EMAIL', 'hello@example.com'),
        'phone' => env('COMPANY_PHONE', '+44 20 7946 0100'),
        'address' => [
            'street' => '4 Kingsway Studios',
            'locality' => 'Manchester',
            'region' => 'Greater Manchester',
            'postal_code' => 'M1 4BT',
            'country' => 'GB',
        ],
    ],

    'social' => [
        'linkedin' => env('SOCIAL_LINKEDIN', 'https://www.linkedin.com/'),
        'github' => env('SOCIAL_GITHUB', 'https://github.com/'),
    ],

    'hero' => [
        'title' => 'Technology that behaves predictably under pressure',
        'lede' => 'We are an independent consultancy for organisations whose systems have outgrown the way they were built. Platform modernisation, data engineering and security work, delivered by a small senior team.',
    ],

    /*
    | Keys double as the allowlist for the enquiry form's service field.
    */
    'services' => [
        'platform-modernisation' => [
            'name' => 'Platform modernisation',
            'summary' => 'Move off the architecture that is holding you back, without a big-bang rewrite.',
            'body' => [
                'Most platforms do not fail suddenly. They accumulate coupling until a one-line change needs three teams and a weekend. We start by measuring where that friction actually is, then carve the system into pieces that can be changed independently.',
                'Work usually runs as a sequence of reversible steps: characterisation tests around the risky parts, a seam introduced, traffic shifted behind a flag, the old path retired once the numbers agree. You keep shipping product throughout.',
            ],
            'deliverables' => [
                'Architecture assessment with a ranked remediation backlog',
                'Strangler-pattern migration plan with reversible checkpoints',
                'Characterisation test suite around the legacy core',
                'Handover documentation and team enablement sessions',
            ],
        ],
        'data-engineering' => [
            'name' => 'Data engineering',
            'summary' => 'Pipelines your analysts trust, and a warehouse that answers questions the same way twice.',
            'body' => [
                'Reporting disagreements are usually a modelling problem wearing a dashboard costume. We model the domain first, then build the ingestion around it, so the numbers reconcile by construction rather than by reconciliation meeting.',
                'Everything is version-controlled, tested and observable. If a pipeline fails at 03:00, the on-call engineer gets an actionable alert rather than a wall of stack traces.',
            ],
            'deliverables' => [
                'Dimensional model documented and agreed with the business',
                'Idempotent, replayable ingestion pipelines',
                'Data quality tests wired into the deployment pipeline',
                'Freshness and volume alerting with runbooks',
            ],
        ],
        'security-engineering' => [
            'name' => 'Security engineering',
            'summary' => 'Threat modelling, hardening and review work that changes the code, not just the slide deck.',
            'body' => [
                'A report that lists findings is the easy half. We work inside the codebase: fixing the authorisation gaps, tightening the headers, replacing the risky patterns, and adding the regression tests that stop them coming back.',
                'Engagements typically begin with a threat model of the paths that actually carry value — authentication, payments, admin tooling, data export — rather than an undirected sweep.',
            ],
            'deliverables' => [
                'Threat model covering the trust boundaries that matter',
                'Prioritised findings with working fixes, not just descriptions',
                'Regression tests for every issue closed',
                'CI security gates and dependency policy',
            ],
        ],
        'cloud-reliability' => [
            'name' => 'Cloud & reliability',
            'summary' => 'Infrastructure that is boring on purpose, with costs you can explain to finance.',
            'body' => [
                'We treat reliability as a product requirement with a number attached. That means service level objectives agreed with the business, error budgets that actually gate releases, and alerting tuned so that a page means someone must act.',
                'On cost: most overspend is architectural, not a tagging problem. We look at the shape of the workload before recommending a single instance-size change.',
            ],
            'deliverables' => [
                'Infrastructure as code with peer-reviewed change flow',
                'SLOs, error budgets and meaningful alerting',
                'Disaster recovery plan with a rehearsed restore',
                'Cost model mapped to product surfaces',
            ],
        ],
    ],

    'budget_bands' => [
        'under-25k' => 'Under £25,000',
        '25k-75k' => '£25,000 – £75,000',
        '75k-150k' => '£75,000 – £150,000',
        'over-150k' => 'Over £150,000',
        'not-sure' => 'Not sure yet',
    ],

    'approach' => [
        [
            'title' => 'Short discovery',
            'body' => 'Two weeks, fixed fee, senior engineers reading the actual code and talking to the actual team. You get a written assessment whether or not you continue with us.',
        ],
        [
            'title' => 'Small senior teams',
            'body' => 'Two to four people who have done this before. No pyramid staffing, no one learning on your budget.',
        ],
        [
            'title' => 'Reversible steps',
            'body' => 'Work is sequenced so that any individual change can be rolled back inside a day. Large irreversible migrations are a failure of planning.',
        ],
        [
            'title' => 'Handover from day one',
            'body' => 'Documentation and pairing are part of delivery, not a phase at the end that gets cut. The goal is that you do not need us afterwards.',
        ],
    ],

    'stats' => [
        ['value' => '11', 'label' => 'Years operating'],
        ['value' => '40+', 'label' => 'Engagements delivered'],
        ['value' => '6', 'label' => 'Senior engineers'],
        ['value' => '94%', 'label' => 'Clients who return'],
    ],

    'case_studies' => [
        'logistics-replatform' => [
            'name' => 'Replatforming a logistics scheduler',
            'sector' => 'Logistics',
            'summary' => 'A nightly batch scheduler that had grown to 40 minutes of critical-path runtime, reduced to under four.',
            'challenge' => 'The overnight run had crept past its window. When it overran, the morning despatch ran on yesterday\'s plan, and the operations team rebuilt routes by hand.',
            'approach' => 'We instrumented the run first and found two thirds of the time in a single N+1 query path. Rather than rewrite the scheduler, we introduced a read model, moved the hot path onto it, and parallelised the independent stages.',
            'outcome' => 'Runtime fell from 40 minutes to 3 minutes 40 seconds. The team removed the manual fallback process entirely.',
            'metrics' => [
                ['value' => '91%', 'label' => 'Runtime reduction'],
                ['value' => '0', 'label' => 'Manual rebuilds since'],
                ['value' => '9 wks', 'label' => 'Engagement length'],
            ],
        ],
        'payments-hardening' => [
            'name' => 'Hardening a payments admin panel',
            'sector' => 'Financial services',
            'summary' => 'A security review that found broken object-level authorisation, and the remediation that closed it.',
            'challenge' => 'An internal tool had grown from three users to ninety across four teams, with authorisation still expressed as a single is_staff boolean.',
            'approach' => 'We threat-modelled the panel, introduced policy-based authorisation per resource, added a tamper-evident audit trail, and enforced TOTP for every operator. Each finding shipped with a regression test.',
            'outcome' => 'Twelve authorisation defects closed, including four that allowed cross-team record access. The audit trail has since been used in two genuine investigations.',
            'metrics' => [
                ['value' => '12', 'label' => 'Defects closed'],
                ['value' => '100%', 'label' => 'Operators on 2FA'],
                ['value' => '6 wks', 'label' => 'Engagement length'],
            ],
        ],
        'warehouse-rebuild' => [
            'name' => 'Rebuilding a reporting warehouse',
            'sector' => 'Retail',
            'summary' => 'Three dashboards, three different revenue figures. One model, and a single agreed number.',
            'challenge' => 'Finance, trading and the board pack each calculated revenue differently. Nobody could say which was correct, so all three were quietly distrusted.',
            'approach' => 'We ran a modelling workshop to agree the definitions, rebuilt ingestion as idempotent and replayable, and put data quality tests in the deployment pipeline so a breaking change fails before it lands.',
            'outcome' => 'One revenue definition across the business. Month-end reporting moved from four days to one.',
            'metrics' => [
                ['value' => '1', 'label' => 'Agreed definition'],
                ['value' => '75%', 'label' => 'Faster month-end'],
                ['value' => '14 wks', 'label' => 'Engagement length'],
            ],
        ],
    ],

    'team' => [
        ['name' => 'A. Okonkwo', 'role' => 'Principal engineer', 'focus' => 'Distributed systems, migration strategy'],
        ['name' => 'M. Lindqvist', 'role' => 'Principal engineer', 'focus' => 'Data modelling, warehouse design'],
        ['name' => 'R. Haddad', 'role' => 'Security lead', 'focus' => 'Threat modelling, application security'],
        ['name' => 'J. Ferreira', 'role' => 'Reliability lead', 'focus' => 'SLOs, incident response, cost'],
    ],

    'faqs' => [
        ['q' => 'How do engagements usually start?', 'a' => 'With a two-week fixed-fee discovery. You get a written assessment at the end of it, and you are free to take that away and act on it yourself.'],
        ['q' => 'Do you work alongside our existing team?', 'a' => 'Almost always. We pair with your engineers rather than working in a separate stream, because the handover is the point.'],
        ['q' => 'What happens to the code you write?', 'a' => 'It is yours. We assign all IP on delivery and work in your repositories under your review process.'],
        ['q' => 'Can you sign our security paperwork?', 'a' => 'Yes. We carry professional indemnity cover and are happy to work under your DPA, NDA and supplier security requirements.'],
    ],
];
