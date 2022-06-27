<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);
        
        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth('api')->user());
    }
    public function roles()
    {
      $permission = [];      
            $home = ['text' => 'Home',
                'icon' => 'home',
                'route' => '/', ];
           if (\Auth::user()->hasRole(['Power Interruption Admin','Super Admin'])){
                $powerInterruption = [
                    'text' => 'Power Interruption',
                    'icon' => 'power',
                    'route' => '/power-interruptions',
                ];
           } 
           if (\Auth::user()->hasRole(['Pending Admin',
                                       'Pending User',
                                       'Super Admin'])){
                $pendingTransaction = [
                    'text' => 'Pending Transaction',
                    'icon' => 'account_tree',
                    'route' => '/pending-transactions',
                ];
            } 
            if (\Auth::user()->hasRole(['Super Admin'])){
                $Administrative = [
                    'text' => 'Administrative',
                    'icon' => 'business_center',
                    'subLinks' => 
                    [
                        0 => 
                        [
                        'text' => 'Users',
                        'links' => 
                        [
                            0 => 
                            [
                            'text' => 'Management',
                            'icon' => 'account_circle',
                            'route' => '/users',
                            ],
                            1 => 
                            [
                            'text' => 'Employments',
                            'icon' => 'assignment',
                            'route' => '/user-employments',
                            ],
                            2 => 
                            [
                            'text' => 'Divisions',
                            'icon' => 'portrait',
                            'route' => '/divisions',
                            ],
                            3 => 
                            [
                            'text' => 'Departments',
                            'icon' => 'portrait',
                            'route' => '/departments',
                            ],
                            4 => 
                            [
                            'text' => 'Positions',
                            'icon' => 'assignment_ind',
                            'route' => '/positions',
                            ],
                        ],
                        ],
                        1 => 
                        [
                        'text' => 'Authorizations',
                        'links' => 
                        [
                            0 => 
                            [
                            'text' => 'Roles',
                            'icon' => 'verified_user',
                            'route' => '/roles',
                            ],
                            1 => 
                            [
                            'text' => 'Permissions',
                            'icon' => 'lock',
                            'route' => '/permissions',
                            ],
                        ],
                        ],
                        2 => 
                        [
                        'text' => 'Branches',
                        'links' => 
                        [
                            0 => 
                            [
                            'text' => 'Management',
                            'icon' => 'store_mall_directory',
                            'route' => '/branches',
                            ],
                            1 => 
                            [
                            'text' => 'Schedules',
                            'icon' => 'access_time',
                            'route' => '/branch-schedules',
                            ],
                            2 => 
                            [
                            'text' => 'Regions',
                            'icon' => 'terrain',
                            'route' => '/regions',
                            ],
                        ],
                        ],
                    ],
                ];
        } 
        if (\Auth::user()->hasRole(['Service Call Admin',
        'Service Call Computerware Admin',
        'Service Call Connectivity Admin',
        'Service Call Admin','Computerware Tickets Admin','Super Admin'])){
            $Service_Call = [
                'text' => 'Service Call',
                'icon' => 'perm_phone_msg',
                'subLinks' => 
                [
                  0 => 
                  [
                    'text' => 'Tickets',
                    'links' => 
                    [
                      0 => 
                      [
                        'text' => 'Connectivity',
                        'icon' => 'turned_in',
                        'route' => '/connectivity-tickets',
                      ],
                      1 => 
                      [
                        'text' => 'Computerware',
                        'icon' => 'turned_in',
                        'route' => '/computerware-tickets',
                      ],
                    ],
                  ],
                  1 => 
                  [
                    'text' => 'Services',
                    'links' => 
                    [
                      0 => 
                      [
                        'text' => 'Providers',
                        'icon' => 'turned_in',
                        'route' => '/service-providers',
                      ],
                      1 => 
                      [
                        'text' => 'Types',
                        'icon' => 'turned_in',
                        'route' => '/service-types',
                      ],
                      2 => 
                      [
                        'text' => 'Categories',
                        'icon' => 'turned_in',
                        'route' => '/service-categories',
                      ],
                    ],
                  ],
                  2 => 
                  [
                    'text' => 'Products',
                    'links' => 
                    [
                      0 => 
                      [
                        'text' => 'Items',
                        'icon' => 'turned_in',
                        'route' => '/product-items',
                      ],
                      1 => 
                      [
                        'text' => 'Brands',
                        'icon' => 'turned_in',
                        'route' => '/product-brands',
                      ],
                      2 => 
                      [
                        'text' => 'Categories',
                        'icon' => 'turned_in',
                        'route' => '/product-categories',
                      ],
                    ],
                  ],
                ],
            ];
        }    
        $css_perm = []; 
 
        if (\Auth::user()->hasRole(['Credit Standing Access'])){
        $cportal = [
          'text' => 'Credit Standing',
          'links' => 
          [
            0 => 
            [
              'text' => 'Dashboard',
              'icon' => 'folder_shared',
              'route' => '/ccs/creditstanding/dashboard',
            ] 
          ],
        ];
      }
        if (\Auth::user()->hasRole(['Aging Recon Access'])){
        $recon = [
          'text' => 'Aging Reconciliation',
          'links' => 
          [
            0 => 
            [
              'text' => 'Recon/Disc',
              'icon' => 'folder_shared',
              'route' => '/ccs/reconciliation/dashboard',
            ] 
          ],
        ];
      }
        if (\Auth::user()->hasRole(['Installment Due Access'])){
                        $installment = [
                          'text' => 'Installment Due',
                          'links' => 
                          [
                            0 => 
                            [
                              'text' => 'Incoming Payment',
                              'icon' => 'folder_shared',
                              'route' => '/ccs/installment/dashboard',
                            ] 
                          ],
                        ];
        }
        if (\Auth::user()->hasRole(['BlackListed Customer Portal Admin',
                                    'BlackListed Customer Portal User'])){
            $blc = [
              'text' => 'Black Listed Customer',
              'links' => 
              [
                0 => 
                [
                  'text' => 'Main',
                  'icon' => 'block',
                  'route' => '/ccs/blacklisted/dashboard',
                ] 
              ],
            ];  
        }
        if (\Auth::user()->hasRole(['CDR Branch',
                                    'CDR Main Office'])){
        $cdr = [
          'text' => 'Customer Digitized Req',
          'links' => 
          [
            0 => 
            [
              'text' => 'CDR',
              'icon' => 'folder_shared',
              'route' => '/ccs/customerdigitized/dashboard',
            ] 
          ],
        ];  
        }
        array_push($css_perm, @$cportal,@$blc,@$cdr, @$installment, @$recon);              
        if (\Auth::user()->hasRole([
        'CDR Main Office',
        'CDR Branch',
        'BlackListed Customer Portal Admin',
        'BlackListed Customer Portal User',
        'Installment Due Access',
        'Aging Recon Access',
        'Credit Standing Access'])){
              $ccs = [
                'text' => 'Credit & Collection',
                'icon' => 'description',
                'subLinks' => array_filter($css_perm)
              ];
         }
                $data = [];
                if (\Auth::user()->hasRole(['Archived Admin',
                  'Archived User'
                  ])) {
                  $dataArchived =  
                  [
                    'text' => 'Archived',
                    'icon' => 'library_books',
                    'route' => '/archived',
                  ];
                }
                if (\Auth::user()->hasRole(['Agencies Admin User',
                  'Agencies Branch User',
                  'Agencies Guest User'])) {
                  $dataAgency = [
                    'text' => 'Agencies',
                    'icon' => 'file_download',
                    'route' => '/agencies',
                  ];
                }
                  array_push($data, @$dataArchived, @$dataAgency);
                  if (\Auth::user()->hasRole(['Archived Admin',
                  'Archived User','Agencies Admin User',
                  'Agencies Branch User',
                  'Agencies Guest User'
                  ])) {
                  $govengency = [
                        'text' => 'Gov\'t. Agency',
                        'icon' => 'groups',
                        'subLinks' => 
                        [
                          0 => 
                          [
                            'text' => 'Reports',
                            'links' => array_filter($data),
                          ],
                        ]
                    ];
                  }
                  
 
          array_push($permission, @$home, @$pendingTransaction, @$Administrative, @$Service_Call, @$govengency, @$ccs );
             
           
 
      return array_filter($permission);
    }
    public function permission(){
     return $permission = \Auth::user()->getAllPermissions()->pluck('name');
    }
    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        $user = $this->guard()->user();
        return response()->json([
            'access_token' => $token,
            'user' => $user->select('id',
                                    'branch_id',
                                    'first_name',
                                    'last_name',
                                    \DB::raw("CONCAT(first_name,' ',last_name) AS full_name"))
                           ->with('branch')
                           ->with(['employment' => function ($qry) {
                             $qry->with('position')->with('department');
                           }])
                           ->where('id', $user->id)
                           ->first(),
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 300
        ]);
    }

    public function guard() {
        return \Auth::Guard('api');
    }
}
 