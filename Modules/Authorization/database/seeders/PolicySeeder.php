<?php

namespace Modules\Authorization\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Authorization\Models\Policy;

class PolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $policies = [
            // Geographic Access Policy - Tree Structure
            [
                'name' => 'Geographic Access Policy',
                'resource' => 'locations',
                'actions' => ['read', 'write'],
                'scope' => 'regional',
                'group' => 'security',
                'is_active' => true,
                'priority' => 100,
                'conditions' => [
                    'geography' => [
                        'country' => 'Indonesia',
                        'regions' => [
                            'province' => 'DKI Jakarta',
                            'cities' => ['Jakarta Pusat', 'Jakarta Utara', 'Jakarta Barat'],
                            'districts' => [
                                'allowed' => ['Gambir', 'Tanah Abang', 'Menteng'],
                                'restricted' => ['Slipi', ' Palmerah']
                            ]
                        ]
                    ],
                    'time_restrictions' => [
                        'business_hours' => [
                            'start' => '08:00',
                            'end' => '18:00',
                            'timezone' => 'Asia/Jakarta'
                        ]
                    ]
                ],
                'module' => 'location_management',
                'description' => 'Policy with hierarchical geographic restrictions forming a tree structure'
            ],

            // Organizational Hierarchy Policy
            [
                'name' => 'Organizational Access Policy',
                'resource' => 'company_data',
                'actions' => ['read', 'write', 'delete'],
                'scope' => 'organizational',
                'group' => 'business_logic',
                'is_active' => true,
                'priority' => 90,
                'conditions' => [
                    'organization' => [
                        'company' => [
                            'id' => 'COMP001',
                            'name' => 'PT Example Corp',
                            'departments' => [
                                'IT' => [
                                    'teams' => ['Development', 'DevOps', 'Security'],
                                    'access_level' => 'full'
                                ],
                                'HR' => [
                                    'teams' => ['Recruitment', 'Employee Relations'],
                                    'access_level' => 'limited'
                                ],
                                'Finance' => [
                                    'teams' => ['Accounting', 'Budgeting'],
                                    'access_level' => 'restricted'
                                ]
                            ]
                        ]
                    ],
                    'user_attributes' => [
                        'employment_status' => 'active',
                        'tenure_years' => ['min' => 1, 'max' => 10]
                    ]
                ],
                'module' => 'organization',
                'description' => 'Policy with organizational hierarchy tree structure'
            ],

            // Time-based Access Policy
            [
                'name' => 'Time-based Access Policy',
                'resource' => 'sensitive_operations',
                'actions' => ['execute'],
                'scope' => 'temporal',
                'group' => 'security',
                'is_active' => true,
                'priority' => 80,
                'conditions' => [
                    'time_windows' => [
                        'business_days' => [
                            'days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
                            'hours' => [
                                'morning' => ['start' => '08:00', 'end' => '12:00'],
                                'afternoon' => ['start' => '13:00', 'end' => '17:00']
                            ]
                        ],
                        'exceptions' => [
                            'holidays' => ['2024-12-25', '2024-01-01'],
                            'maintenance_windows' => [
                                'weekly' => 'sunday 02:00-04:00',
                                'monthly' => 'last_sunday 01:00-03:00'
                            ]
                        ]
                    ],
                    'user_clearance' => [
                        'level' => ['high', 'critical'],
                        'last_training' => [
                            'max_age_days' => 90,
                            'mandatory_topics' => ['security', 'compliance']
                        ]
                    ]
                ],
                'module' => 'security',
                'description' => 'Policy with complex time-based tree conditions'
            ],

            // Resource-based Access Policy
            [
                'name' => 'Resource Hierarchy Policy',
                'resource' => 'files',
                'actions' => ['read', 'write', 'delete'],
                'scope' => 'resource_based',
                'group' => 'data_access',
                'is_active' => true,
                'priority' => 70,
                'conditions' => [
                    'resource_hierarchy' => [
                        'file_system' => [
                            'root' => '/company_data',
                            'directories' => [
                                'public' => [
                                    'access' => 'unrestricted',
                                    'subdirs' => ['marketing', 'sales']
                                ],
                                'confidential' => [
                                    'access' => 'role_based',
                                    'subdirs' => [
                                        'financial' => ['allowed_roles' => ['finance', 'executive']],
                                        'hr' => ['allowed_roles' => ['hr', 'management']],
                                        'technical' => [
                                            'subdirs' => [
                                                'source_code' => ['allowed_roles' => ['developer', 'architect']],
                                                'infrastructure' => ['allowed_roles' => ['devops', 'sysadmin']]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'file_attributes' => [
                        'max_size_mb' => 100,
                        'allowed_extensions' => ['pdf', 'doc', 'xls', 'txt'],
                        'encryption_required' => true
                    ]
                ],
                'module' => 'file_management',
                'description' => 'Policy with resource hierarchy tree structure'
            ],

            // Multi-tenant Access Policy
            [
                'name' => 'Multi-tenant Isolation Policy',
                'resource' => 'tenant_data',
                'actions' => ['read', 'write'],
                'scope' => 'multi_tenant',
                'group' => 'tenant_isolation',
                'is_active' => true,
                'priority' => 60,
                'conditions' => [
                    'tenant_hierarchy' => [
                        'tenant' => [
                            'id' => 'current_user.tenant_id',
                            'type' => ['enterprise', 'professional', 'basic'],
                            'features' => [
                                'enabled' => ['api_access', 'reporting', 'multi_user'],
                                'disabled' => ['advanced_analytics', 'custom_integrations']
                            ],
                            'sub_tenants' => [
                                'allowed' => true,
                                'max_depth' => 3,
                                'inheritance' => [
                                    'permissions' => 'cascading',
                                    'settings' => 'override_allowed'
                                ]
                            ]
                        ]
                    ],
                    'cross_tenant_access' => [
                        'allowed' => false,
                        'exceptions' => [
                            'parent_tenant' => true,
                            'partner_tenants' => ['partner_001', 'partner_002']
                        ]
                    ]
                ],
                'module' => 'multi_tenant',
                'description' => 'Policy with multi-tenant hierarchy tree structure'
            ]
        ];

        foreach ($policies as $policy) {
            Policy::updateOrCreate(
                ['name' => $policy['name']],
                $policy
            );
        }
    }
}
