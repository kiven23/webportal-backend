<aside class="main-sidebar">
  <section class="sidebar">
    <div class="user-panel">
      <div class="pull-left image">
        <img src="{{ asset('images/addessa_logo.png') }}" class="img-circle" alt="User Image">
      </div>
      <div class="pull-left info">
        <p>{{ \Auth::user()->first_name }} {{ \Auth::user()->last_name }}</p>
        <a href="#"><i class="fa fa-circle text-success"></i> {{ \Auth::user()->company ? \Auth::user()->company->name : 'Not Assigned' }}</a>
      </div>
    </div>

    <form action="#" method="get" class="sidebar-form">
      <div class="input-group">
        <input type="text" name="q" class="form-control" placeholder="Search...">
        <span class="input-group-btn">
          <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
          </button>
        </span>
      </div>
    </form>

    <ul class="sidebar-menu" data-widget="tree">
      <!-- Start of Dashboard -->
      <li class="{{ \Request::route()->getName() === 'home' ? 'active' : '' }}">
        <a href="{{ route('home') }}">
          <i class="fa fa-dashboard"></i> <span>Dashboard</span>
        </a>
      </li>
      <!-- End of Dashboard -->

<!-- --------------------------------------------------------------------- -->
      
      <!-- ---------------------------- -->
      <!-- Start of Online Filing Links -->
      <!-- ---------------------------- -->
      @if (\Auth::user()->hasAnyPermission([
        'Show Overtimes',
        'Create Overtimes',
        'Edit Overtimes',
        'Delete Overtimes',
        'Approve Overtimes',
        'Return Overtimes',
        'Reject Overtimes',
        'Overlook Overtimes',
        'Show Leave of Absences',
        'Create Leave of Absences',
        'Edit Leave of Absences',
        'Delete Leave of Absences',
        'Approve Leave of Absences',
        'Return Leave of Absences',
        'Reject Leave of Absences',
        'Overlook Leave of Absences'
      ]))
        <li class="header">ONLINE FILING</li>
        <!-- Start of Overtime -->
        @if (\Auth::user()->hasAnyPermission([
          'Show Overtimes',
          'Create Overtimes',
          'Edit Overtimes',
          'Delete Overtimes',
          'Approve Overtimes',
          'Return Overtimes',
          'Reject Overtimes',
          'Overlook Overtimes',
        ]))
          <li class="treeview {{ isset($is_overtime_filing_route) ? 'active' : (isset($is_overtime_approval_route) ? 'active' : '') }}">
            <a href="#"><i class="fa fa-hourglass-1"></i> <span>Overtime</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              @if (\Auth::user()->hasAnyPermission([
                'Show Overtimes',
                'Create Overtimes',
                'Edit Overtimes',
                'Delete Overtimes',
              ]))
                <li class="{{ isset($is_overtime_filing_route) ? 'active' : '' }}">
                  <a href="{{ route('overtimes') }}"><i class="fa fa-circle-o"></i>&nbsp;Filing</a>
                </li>
              @endif
              @if (\Auth::user()->hasAnyPermission([
                'Approve Overtimes',
                'Return Overtimes',
                'Reject Overtimes',
              ]))
                <li class="{{ isset($is_overtime_approval_route) && \Request::route()->getName() !== 'approval.overlook' ? 'active' : '' }}">
                  <a href="{{ route('approval.pending') }}"><i class="fa fa-circle-o"></i>&nbsp;Approvals</a>
                </li>
              @endif
              @if (\Auth::user()->hasAnyPermission([
                'Overlook Overtimes',
              ]))
                <li class="{{ \Request::route()->getName() === 'approval.overlook' ? 'active' : '' }}">
                  <a href="{{ route('approval.overlook') }}"><i class="fa fa-circle-o"></i>&nbsp;Overlooks</a>
                </li>
              @endif
            </ul>
          </li>
        @endif
        <!-- End of Overtime -->

        <!-- Start of Leave of Absence -->
        <li>
          <a href="javascript:void(0);">
            <i class="fa fa-suitcase"></i> <span>Leave of Absence</span>
          </a>
        </li>
      @endif
      <!-- End of Leave of Absence -->
      <!-- -------------------------- -->
      <!-- End of Online Filing Links -->
      <!-- -------------------------- -->

<!-- --------------------------------------------------------------------- -->
      
      <!-- ----------------------------- -->
      <!-- Start of Administrative Links -->
      <!-- ----------------------------- -->
      @if (\Auth::user()->hasAnyPermission([
        'Administer roles & permissions',

        'Show Companies',
        'Create Companies',
        'Edit Companies',
        'Delete Companies',

        'Show Branches',
        'Create Branches',
        'Edit Branches',
        'Delete Branches',

        'Show Branch Schedules',
        'Create Branch Schedules',
        'Edit Branch Schedules',
        'Delete Branch Schedules',

        'Show Regions',
        'Create Regions',
        'Edit Regions',
        'Delete Regions',

        'Show Users',
        'Create Users',
        'Edit Users',
        'Delete Users',

        'Show User Employments',
        'Edit User Employments',

        'Show User Authorizations',
        'Edit User Authorizations',

        'Show Divisions',
        'Create Divisions',
        'Edit Divisions',
        'Delete Divisions',

        'Show Departments',
        'Create Departments',
        'Edit Departments',
        'Delete Departments',

        'Show Positions',
        'Create Positions',
        'Edit Positions',
        'Delete Positions',

        'Show Access Charts',
        'Create Access Charts',
        'Edit Access Charts',
        'Delete Access Charts',

        'Approve Access Charts',
        'Assign Access Charts',

        'Show File Types',
        'Create File Types',
        'Edit File Types',
        'Delete File Types'
      ]))
        <li class="header">ADMINISTRATIVE</li>
        <!-- Start of Companies -->
        @if (\Auth::user()->hasAnyPermission([
          'Show Companies',
          'Create Companies',
          'Edit Companies',
          'Delete Companies'
        ]))
          <li class="{{ isset($is_company_route) ? 'active' : '' }}">
            <a href="{{ route('companies.index') }}">
              <i class="fa fa-building"></i> <span>Companies</span>
            </a>
          </li>
        <!-- End of Companies -->
        @endif

        <!-- Start of Branch Links -->
        @if (\Auth::user()->hasAnyPermission([
          'Show Branches',
          'Create Branches',
          'Edit Branches',
          'Delete Branches',

          'Show Branch Schedules',
          'Create Branch Schedules',
          'Edit Branch Schedules',
          'Delete Branch Schedules',

          'Show Regions',
          'Create Regions',
          'Edit Regions',
          'Delete Regions',
        ]))
          <li class="treeview {{ isset($is_branch_route) ? 'active' : (isset($is_region_route) ? 'active' : (isset($is_bsched_route) ? 'active' : '')) }}">
            <a href="#"><i class="fa fa-home"></i> <span>Branches</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              @if (\Auth::user()->hasAnyPermission([
                'Show Branches',
                'Create Branches',
                'Edit Branches',
                'Delete Branches',
              ]))
                <li class="{{ isset($is_branch_route) ? 'active' : '' }}">
                  <a href="{{ route('branches.index') }}"><i class="fa fa-circle-o"></i>&nbsp;Branch Lists</a>
                </li>
              @endif
              @if (\Auth::user()->hasAnyPermission([
                'Show Branch Schedules',
                'Create Branch Schedules',
                'Edit Branch Schedules',
                'Delete Branch Schedules',
              ]))
                <li class="{{ isset($is_bsched_route) ? 'active' : '' }}">
                  <a href="{{ route('branch-schedules.index') }}"><i class="fa fa-circle-o"></i>&nbsp;Schedules</a>
                </li>
              @endif
              @if (\Auth::user()->hasAnyPermission([
                'Show Regions',
                'Create Regions',
                'Edit Regions',
                'Delete Regions',
              ]))
                <li class="{{ isset($is_region_route) ? 'active' : '' }}">
                  <a href="{{ route('regions') }}"><i class="fa fa-circle-o"></i>&nbsp;Regions</a>
                </li>
              @endif
            </ul>
          </li>
        @endif
        <!-- End of Branch Links -->

        <!-- Start of User Links -->
        @if (\Auth::user()->hasAnyPermission([
          'Show Users',
          'Create Users',
          'Edit Users',
          'Delete Users',

          'Show User Employments',
          'Edit User Employments',

          'Show User Authorizations',
          'Edit User Authorizations',
        ]))
          <li class="treeview {{ isset($is_user_route) ? 'active' : (isset($is_auth_route) ? 'active' : (isset($is_employment_route) ? 'active' : '')) }}">
            <a href="#"><i class="fa fa-gear"></i> <span>User Management</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              @if (\Auth::user()->hasAnyPermission([
                'Show User Authorizations',
                'Edit User Authorizations',
              ]))
                <li class="{{ isset($is_auth_route) ? 'active' : '' }}">
                  <a href="{{ route('authorizations.index') }}"><i class="fa fa-circle-o"></i>&nbsp;Authorization</a>
                </li>
              @endif
              @if (\Auth::user()->hasAnyPermission([
                'Show Users',
                'Create Users',
                'Edit Users',
                'Delete Users',
              ]))
                <li class="{{ isset($is_user_route) ? 'active' : '' }}">
                  <a href="{{ route('users.index') }}"><i class="fa fa-circle-o"></i>&nbsp;User Accounts</a>
                </li>
              @endif
              @if (\Auth::user()->hasAnyPermission([
                'Show User Employments',
                'Edit User Employments',
              ]))
                <li class="{{ isset($is_employment_route) ? 'active' : '' }}">
                  <a href="{{ route('employment_details.index') }}"><i class="fa fa-circle-o"></i>&nbsp;Employment Details</a>
                </li>
              @endif
            </ul>
          </li>
        @endif
        <!-- End of User Links -->

        <!-- Start of Authorization Links -->
        @if (\Auth::user()->hasAnyPermission(['Administer roles & permissions']))
          <li class="treeview {{ isset($is_role_route) ? 'active' : (isset($is_permission_route) ? 'active' : '') }}">
            <a href="#"><i class="fa fa-shield"></i> <span>Authorizations</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li class="{{ isset($is_role_route) ? 'active' : '' }}">
                <a href="{{ route('roles.index') }}"><i class="fa fa-circle-o"></i>&nbsp;Roles</a>
              </li>
              <li class="{{ isset($is_permission_route) ? 'active' : '' }}">
                <a href="{{ route('permissions.index') }}"><i class="fa fa-circle-o"></i>&nbsp;Permissions</a>
              </li>
            </ul>
          </li>
        @endif
        <!-- End of Authorization Links -->

        <!-- Start of Division Links -->
        @if (\Auth::user()->hasAnyPermission([
          'Show Divisions',
          'Create Divisions',
          'Edit Divisions',
          'Delete Divisions',
        ]))
          <li class="{{ isset($is_division_route) ? 'active' : '' }}">
            <a href="{{ route('divisions.index') }}">
              <i class="fa fa-sitemap"></i> <span>Divisions</span>
            </a>
          </li>
        @endif
        <!-- End of Department Links -->

        <!-- Start of Department Links -->
        @if (\Auth::user()->hasAnyPermission([
          'Show Departments',
          'Create Departments',
          'Edit Departments',
          'Delete Departments',
        ]))
          <li class="{{ isset($is_department_route) ? 'active' : '' }}">
            <a href="{{ route('departments.index') }}">
              <i class="fa fa-sitemap"></i> <span>Departments</span>
            </a>
          </li>
        @endif
        <!-- End of Department Links -->

        <!-- Start of Position Links -->
        @if (\Auth::user()->hasAnyPermission([
          'Show Positions',
          'Create Positions',
          'Edit Positions',
          'Delete Positions',
        ]))
          <li class="{{ isset($is_position_route) ? 'active' : '' }}">
            <a href="{{ route('positions.index') }}">
              <i class="fa fa-user-secret"></i> <span>Positions</span>
            </a>
          </li>
        @endif
        <!-- End of Position Links -->

        <!-- Start of Access Chart Links -->
        @if (\Auth::user()->hasAnyPermission([
          'Show Access Charts',
          'Create Access Charts',
          'Edit Access Charts',
          'Delete Access Charts',

          'Show Approving Officers',
          'Assign Approving Officers',
        ]))
          <li class="{{ isset($is_access_chart_route) ? 'active' : '' }}">
            <a href="{{ route('access_charts.index') }}">
              <i class="fa fa-vcard"></i> <span>Access Charts</span>
            </a>
          </li>
        @endif
        <!-- End of File Type Links -->

        <!-- Start of File Type Links -->
        @if (\Auth::user()->hasAnyPermission([
          'Show File Types',
          'Create File Types',
          'Edit File Types',
          'Delete File Types',
        ]))
          <li class="{{ isset($is_file_type_route) ? 'active' : '' }}">
            <a href="{{ route('file.type.index') }}">
              <i class="fa fa-file"></i> <span>File Types</span>
            </a>
          </li>
        @endif
        <!-- End of File Type Links -->
      @endif
      <!-- --------------------------- -->
      <!-- End of Administrative Links -->
      <!-- --------------------------- -->

<!-- --------------------------------------------------------------------- -->

      <!-- --------------------- -->
      <!-- Start of Report Links -->
      <!-- --------------------- -->
      @if (\Auth::user()->hasAnyPermission([
        'Generate Biometric Reports',
        'Generate DTR Reports',
        'Generate Overtime Reports',

        'Show Purchasing Reports',
        'Create Purchasing Reports',
        'Edit Purchasing Reports',
        'Delete Purchasing Reports',

        'View Purchasing Reports'
      ]))
        <li class="header">REPORT</li>
        <!-- Start of Biometric Links -->
        @if (\Auth::user()->hasAnyPermission([
          'Generate Biometric Reports'
        ]))
          <li class="{{ isset($is_biometric_route) ? 'active' : '' }}">
            <a href="{{ \Auth::user()->branch ? (\Auth::user()->branch->machine_number === 103 ? route('report.biometric') : route('breport.biometric')) : '' }}">
              <i class="fa fa-500px"></i> <span>Biometric</span>
            </a>
          </li>
        @endif
        <!-- End of Biometric Links -->
        <!-- Start of DTR Links -->
        @if (\Auth::user()->hasAnyPermission([
          'Generate DTR Reports'
        ]))
          <li class="{{ isset($is_dtr_route) ? 'active' : '' }}">
            <a href="{{ \Auth::user()->branch ? (\Auth::user()->branch->machine_number === 103 ? route('report.dtr') : route('breport.dtr')) : '' }}">
              <i class="fa fa-clipboard"></i> <span>DTR</span>
            </a>
          </li>
        @endif
        <!-- End of DTR Links -->
        <!-- Start of Overtime Links -->
        @if (\Auth::user()->hasAnyPermission([
          'Generate Overtime Reports'
        ]))
          <li class="{{ isset($is_overtime_route) ? 'active' : '' }}">
            <a href="{{ \Auth::user()->branch ? (\Auth::user()->branch->machine_number === 103 ? route('report.overtime') : route('breport.overtime')) : '' }}">
              <i class="fa fa-clock-o"></i> <span>Overtime</span>
            </a>
          </li>
        @endif
        <!-- End of Overtime Links -->
        <!-- Start of Purchasing Report Links -->
        @if (\Auth::user()->hasAnyPermission([
          'Show Purchasing Reports',
          'Create Purchasing Reports',
          'Edit Purchasing Reports',
          'Delete Purchasing Reports',

          'View Purchasing Reports'
        ]))
          <li class="treeview {{ isset($is_purch_report_route) ? 'active' : '' }}">
            <a href="#"><i class="fa fa-file"></i> <span>Purchasing Reports</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              @if (\Auth::user()->hasPermissionTo('View Purchasing Reports'))
                <li class="{{ isset($is_purch_report_route) && !isset($is_purch_subreport_route) ? 'active' : '' }}">
                  <a href="{{ route('report.purchasing.view') }}"><i class="fa fa-circle-o"></i>&nbsp;All</a>
                </li>
              @else
                <li class="{{ isset($is_purch_report_route) && !isset($is_purch_subreport_route) ? 'active' : '' }}">
                  <a href="{{ route('report.purchasing.index') }}"><i class="fa fa-circle-o"></i>&nbsp;All</a>
                </li>
              @endif
              @foreach ($g_file_types as $type)
                @if (\Auth::user()->hasPermissionTo('View Purchasing Reports'))
                  <li class="{{ isset($is_purch_subreport_route) ? ($is_purch_subreport_route == $type->id ? 'active' : '') : '' }}">
                    <a href="{{ route('report.purchasing.view_subreport', ['id' => $type->id]) }}">
                      <i class="fa fa-circle-o"></i>&nbsp;{{ $type->name }}
                    </a>
                  </li>
                @else
                  <li class="{{ isset($is_purch_subreport_route) ? ($is_purch_subreport_route == $type->id ? 'active' : '') : '' }}">
                    <a href="{{ route('report.purchasing.subreport', ['id' => $type->id]) }}">
                      <i class="fa fa-circle-o"></i>&nbsp;{{ $type->name }}
                    </a>
                  </li>
                @endif
              @endforeach
            </ul>
          </li>
        @endif
        <!-- End of Purchasing Report Links -->
      @endif
      <!-- ------------------- -->
      <!-- End of Report Links -->
      <!-- ------------------- -->

<!-- --------------------------------------------------------------------- -->

      <!-- --------------------------- -->
      <!-- Start of Service Call Links -->
      <!-- --------------------------- -->
      @if (\Auth::user()->hasAnyPermission([
        'Show Computerware Tickets',
        'Create Computerware Tickets',
        'Edit Computerware Tickets',
        'Delete Computerware Tickets',

        'Show Product Items',
        'Create Product Items',
        'Edit Product Items',
        'Delete Product Items',

        'Show Product Brands',
        'Create Product Brands',
        'Edit Product Brands',
        'Delete Product Brands',

        'Show Product Categories',
        'Create Product Categories',
        'Edit Product Categories',
        'Delete Product Categories',


        'Show Connectivity Tickets',
        'Create Connectivity Tickets',
        'Edit Connectivity Tickets',
        'Delete Connectivity Tickets',

        'Show Service Categories',
        'Create Service Categories',
        'Edit Service Categories',
        'Delete Service Categories',

        'Show Service Providers',
        'Create Service Providers',
        'Edit Service Providers',
        'Delete Service Providers',

        'Show Service Types',
        'Create Service Types',
        'Edit Service Types',
        'Delete Service Types',

        'Show Power Interruptions',
        'Create Power Interruptions',
        'Edit Power Interruptions',
        'Delete Power Interruptions',

        'Confirm Connectivity Uptime',
        'Rate Connectivity Tickets',

        'Show Concerns',
        'Create Concerns',
        'Edit Concerns',
        'Delete Concerns',

        'Show Concern Types',
        'Create Concern Types',
        'Edit Concern Types',
        'Delete Concern Types',

        'Show Concern Categories',
        'Create Concern Categories',
        'Edit Concern Categories',
        'Delete Concern Categories',
      ]))
        <li class="header">SERVICE CALL</li>
        <!-- Start of Computerware Links -->
        @if (\Auth::user()->hasAnyPermission([
          'Show Computerware Tickets',
          'Create Computerware Tickets',
          'Edit Computerware Tickets',
          'Delete Computerware Tickets',

          'Show Product Items',
          'Create Product Items',
          'Edit Product Items',
          'Delete Product Items',

          'Show Product Brands',
          'Create Product Brands',
          'Edit Product Brands',
          'Delete Product Brands',

          'Show Product Categories',
          'Create Product Categories',
          'Edit Product Categories',
          'Delete Product Categories',
        ]))
          <li class="treeview
              {{ isset($is_sc_computerware_ticket_route) ? 'active' :
                (isset($is_sc_product_item_route) ? 'active' :
                (isset($is_sc_product_brand_route) ? 'active' :
                (isset($is_sc_product_category_route) ? 'active' : ''))) }}">
            <a href="#"><i class="fa fa-desktop"></i> <span>Computerware</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              @if (\Auth::user()->hasAnyPermission([
                'Show Product Items',
                'Create Product Items',
                'Edit Product Items',
                'Delete Product Items',

                'Show Product Brands',
                'Create Product Brands',
                'Edit Product Brands',
                'Delete Product Brands',

                'Show Product Categories',
                'Create Product Categories',
                'Edit Product Categories',
                'Delete Product Categories',
              ]))
                <li class="treeview
                    {{ isset($is_sc_product_item_route) ? 'active' :
                      (isset($is_sc_product_brand_route) ? 'active' :
                      (isset($is_sc_product_category_route) ? 'active' : '')) }}">
                  <a href="#"><i class="fa fa-circle-o"></i> Product
                    <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    @if (\Auth::user()->hasAnyPermission([
                      'Show Product Items',
                      'Create Product Items',
                      'Edit Product Items',
                      'Delete Product Items',
                    ]))
                      <li class="{{ isset($is_sc_product_item_route) ? 'active' : '' }}">
                        <a href="{{ route('items') }}"><i class="fa fa-circle-o"></i> Items</a>
                      </li>
                    @endif

                    @if (\Auth::user()->hasAnyPermission([
                      'Show Product Brands',
                      'Create Product Brands',
                      'Edit Product Brands',
                      'Delete Product Brands',
                    ]))
                      <li class="{{ isset($is_sc_product_brand_route) ? 'active' : '' }}">
                        <a href="{{ route('brands') }}"><i class="fa fa-circle-o"></i> Brands</a>
                      </li>
                    @endif

                    @if (\Auth::user()->hasAnyPermission([
                      'Show Product Categories',
                      'Create Product Categories',
                      'Edit Product Categories',
                      'Delete Product Categories',
                    ]))
                      <li class="{{ isset($is_sc_product_category_route) ? 'active' : '' }}">
                        <a href="{{ route('categories') }}"><i class="fa fa-circle-o"></i> Category</a>
                      </li>
                    @endif
                  </ul>
                </li>
              @endif

              @if (\Auth::user()->hasAnyPermission([
                'Show Computerware Tickets',
                'Create Computerware Tickets',
                'Edit Computerware Tickets',
                'Delete Computerware Tickets',
              ]))
                <li class="{{ isset($is_sc_computerware_ticket_route) ? 'active' : '' }}">
                  <a href="{{ route('ticket.computerwares') }}"><i class="fa fa-circle-o"></i>&nbsp;Tickets</a>
                </li>
              @endif
            </ul>
          </li>
        @endif
        <!-- End of Computerware Links -->

        <!-- Start of Connectivity Links -->
        @if (\Auth::user()->hasAnyPermission([
          'Show Connectivity Tickets',
          'Create Connectivity Tickets',
          'Edit Connectivity Tickets',
          'Delete Connectivity Tickets',

          'Show Service Categories',
          'Create Service Categories',
          'Edit Service Categories',
          'Delete Service Categories',

          'Show Service Providers',
          'Create Service Providers',
          'Edit Service Providers',
          'Delete Service Providers',

          'Show Service Types',
          'Create Service Types',
          'Edit Service Types',
          'Delete Service Types',

          'Show Power Interruptions',
          'Create Power Interruptions',
          'Edit Power Interruptions',
          'Delete Power Interruptions',

          'Confirm Connectivity Uptime',
          'Rate Connectivity Tickets',
        ]))
          <li class="treeview
              {{ isset($is_sc_connectivity_ticket_route) ? 'active' :
                (isset($is_sc_service_provider_route) ? 'active' :
                (isset($is_sc_service_type_route) ? 'active' :
                (isset($is_sc_service_category_route) ? 'active' :
                (isset($is_sc_power_interruption_route) ? 'active' : '')))) }}">
            <a href="#"><i class="fa fa-wifi"></i> <span>Connectivity</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              @if (\Auth::user()->hasAnyPermission([
                'Show Power Interruptions',
                'Create Power Interruptions',
                'Edit Power Interruptions',
                'Delete Power Interruptions',
              ]))
                <li class="{{ isset($is_sc_power_interruption_route) ? 'active' : '' }}">
                  <a href="{{ route('power_interruptions') }}"><i class="fa fa-circle-o"></i>&nbsp;Power Interruption</a>
                </li>
              @endif

              @if (\Auth::user()->hasAnyPermission([
                'Show Service Categories',
                'Create Service Categories',
                'Edit Service Categories',
                'Delete Service Categories',

                'Show Service Providers',
                'Create Service Providers',
                'Edit Service Providers',
                'Delete Service Providers',

                'Show Service Types',
                'Create Service Types',
                'Edit Service Types',
                'Delete Service Types',
              ]))
                <li class="treeview
                    {{ isset($is_sc_service_provider_route) ? 'active' :
                      (isset($is_sc_service_type_route) ? 'active' :
                      (isset($is_sc_service_category_route) ? 'active' : '')) }}">
                  <a href="#"><i class="fa fa-circle-o"></i> Service
                    <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    @if (\Auth::user()->hasAnyPermission([
                      'Show Service Providers',
                      'Create Service Providers',
                      'Edit Service Providers',
                      'Delete Service Providers',
                    ]))
                      <li class="{{ isset($is_sc_service_provider_route) ? 'active' : '' }}">
                        <a href="{{ route('service_providers') }}"><i class="fa fa-circle-o"></i> Provider</a>
                      </li>
                    @endif

                    @if (\Auth::user()->hasAnyPermission([
                      'Show Service Types',
                      'Create Service Types',
                      'Edit Service Types',
                      'Delete Service Types',
                    ]))
                      <li class="{{ isset($is_sc_service_type_route) ? 'active' : '' }}">
                        <a href="{{ route('service_types') }}"><i class="fa fa-circle-o"></i> Type</a>
                      </li>
                    @endif

                    @if (\Auth::user()->hasAnyPermission([
                      'Show Service Categories',
                      'Create Service Categories',
                      'Edit Service Categories',
                      'Delete Service Categories',
                    ]))
                      <li class="{{ isset($is_sc_service_category_route) ? 'active' : '' }}">
                        <a href="{{ route('service_categories') }}"><i class="fa fa-circle-o"></i> Category</a>
                      </li>
                    @endif
                  </ul>
                </li>
              @endif

              @if (\Auth::user()->hasAnyPermission([
                'Show Connectivity Tickets',
                'Create Connectivity Tickets',
                'Edit Connectivity Tickets',
                'Delete Connectivity Tickets',

                'Confirm Connectivity Uptime',
                'Rate Connectivity Tickets'
              ]))
                @if (\Auth::user()->hasAnyPermission([
                  'Confirm Connectivity Uptime',
                  'Rate Connectivity Tickets'
                ]))
                  <li class="{{ isset($is_sc_connectivity_ticket_route) ? 'active' : '' }}">
                    <a href="{{ route('ticket.branch.connectivities') }}"><i class="fa fa-circle-o"></i>&nbsp;Tickets</a>
                  </li>
                @else
                  <li class="{{ isset($is_sc_connectivity_ticket_route) ? 'active' : '' }}">
                    <a href="{{ route('ticket.connectivities') }}"><i class="fa fa-circle-o"></i>&nbsp;Tickets</a>
                  </li>
                @endif
              @endif
            </ul>
          </li>
        @endif
        <!-- End of Connectivity Links -->

        <!-- Start of Concern Links -->
        @if (\Auth::user()->hasAnyPermission([
          'Show Concerns',
          'Create Concerns',
          'Edit Concerns',
          'Delete Concerns',
          
          'Show Concern Types',
          'Create Concern Types',
          'Edit Concern Types',
          'Delete Concern Types',

          'Show Concern Categories',
          'Create Concern Categories',
          'Edit Concern Categories',
          'Delete Concern Categories',
        ]))
          <li class="treeview
              {{ isset($is_concern_route) ? 'active' :
                (isset($is_concern_type_route) ? 'active' :
                (isset($is_concern_category_route) ? 'active' : '')) }}">
            <a href="#"><i class="fa fa-archive"></i> <span>Concern</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              @if (\Auth::user()->hasAnyPermission([
                'Show Concerns',
                'Create Concerns',
                'Edit Concerns',
                'Delete Concerns',
              ]))
                <li class="{{ isset($is_concern_route) ? 'active' : '' }}">
                  <a href="{{ route('concerns.index') }}"><i class="fa fa-circle-o"></i>&nbsp;Lists</a>
                </li>
              @endif

              @if (\Auth::user()->hasAnyPermission([
                'Show Concern Types',
                'Create Concern Types',
                'Edit Concern Types',
                'Delete Concern Types',
              ]))
                <li class="{{ isset($is_concern_type_route) ? 'active' : '' }}">
                  <a href="{{ route('concerns.types.index') }}"><i class="fa fa-circle-o"></i>&nbsp;Types</a>
                </li>
              @endif

              @if (\Auth::user()->hasAnyPermission([
                'Show Concern Categories',
                'Create Concern Categories',
                'Edit Concern Categories',
                'Delete Concern Categories',
              ]))
                <li class="{{ isset($is_concern_category_route) ? 'active' : '' }}">
                  <a href="{{ route('concerns.categories.index') }}"><i class="fa fa-circle-o"></i>&nbsp;Categories</a>
                </li>
              @endif
            </ul>
          </li>
        @endif
        <!-- End of Concern Links -->
      @endif
      <!-- ------------------------- -->
      <!-- End of Service Call Links -->
      <!-- ------------------------- -->


<!-- --------------------------------------------------------------------- -->

      <!-- ----------------------------- -->
      <!-- Start of Reconciliation Links -->
      <!-- ----------------------------- -->
      @if (\Auth::user()->hasAnyPermission([
        'Show Inventory Breakdown',
        'Show Inventory Discrepancies',
        'Get Inventory Raw Files',
      ]))
        <li class="header">RECONCILIATION</li>
        <!-- Start of Inventory Links -->
        <li class="{{ isset($is_inventory_recon_route) ? 'active' : '' }}">
          <a href="{{ route('inventories') }}">
            <i class="fa fa-briefcase"></i> <span>Inventory</span>
          </a>
        </li>
      @endif
      <!-- End
      <!-- End of Inventory Links -->
      <!-- --------------------------- -->
      <!-- End of Reconciliation Links -->
      <!-- --------------------------- -->

<!-- --------------------------------------------------------------------- -->

      <!-- ----------------------------- -->
      <!-- Start of Purchase Order Links -->
      <!-- ----------------------------- -->
      @if (\Auth::user()->hasAnyPermission([
        'Show Purchase Order Files',
        'View Purchase Order Files',
        'Create Purchase Order Files',
        'Edit Purchase Order Files',
        'Delete Purchase Order Files',

        'Approve Purchase Order Files',
        'Reject Purchase Order Files',
        'Overlook Purchase Order Files',
      ]))
        <li class="header">PURCHASE ORDER</li>
        <!-- Start of Purchase Order Files -->
        @if (\Auth::user()->hasAnyPermission([
          'Show Purchase Order Files',
          'View Purchase Order Files',
          'Create Purchase Order Files',
          'Edit Purchase Order Files',
          'Delete Purchase Order Files',

          'View Approved Purchase Order Files',
          'Approve Purchase Order Files',
          'Reject Purchase Order Files',
          'Overlook Purchase Order Files',
        ]))
          <li class="treeview {{ isset($is_po_file_route) ? 'active' : (isset($is_po_file_approval_route) ? 'active' : '') }}">
            <a href="#"><i class="fa fa-file-o"></i> <span>Files</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              @if (\Auth::user()->hasAnyPermission([
                'Show Purchase Order Files',
                'Create Purchase Order Files',
                'Edit Purchase Order Files',
                'Delete Purchase Order Files',
              ]))
                <li class="{{ isset($is_po_file_route) ? 'active' : '' }}">
                  <a href="{{ route('purchase_orders.files.index') }}"><i class="fa fa-circle-o"></i>&nbsp;Sending</a>
                </li>
              @endif
              @if (\Auth::user()->hasPermissionTo(
                'View Purchase Order Files'
              ))
                <li class="{{ isset($is_po_file_route) ? 'active' : '' }}">
                  <a href="{{ route('purchase_orders.files.view') }}"><i class="fa fa-circle-o"></i>&nbsp;View</a>
                </li>
              @endif
              @if (\Auth::user()->hasPermissionTo(
                'View Approved Purchase Order Files'
              ))
                <li class="{{ isset($is_po_file_route) ? 'active' : '' }}">
                  <a href="{{ route('purchase_orders.files.view_approved') }}"><i class="fa fa-circle-o"></i>&nbsp;Approved PO Files</a>
                </li>
              @endif
              @if (\Auth::user()->hasAnyPermission([
                'Approve Purchase Order Files',
                'Reject Purchase Order Files',
              ]) && !\Auth::user()->hasPermissionTo('Overlook Purchase Order Files'))
                <li class="{{ isset($is_po_file_approval_route) ? 'active' : '' }}">
                  <a href="{{ route('po.file.approval.pending') }}"><i class="fa fa-circle-o"></i>&nbsp;Approvals</a>
                </li>
              @endif
              @if (\Auth::user()->hasAnyPermission([
                'Overlook Purchase Order Files',
              ]))
                <li class="{{ \Request::route()->getName() === 'po.file.approval.overlook' ? 'active' : '' }}">
                  <a href="{{ route('po.file.approval.overlook') }}"><i class="fa fa-circle-o"></i>&nbsp;Overlooks</a>
                </li>
              @endif
            </ul>
          </li>
        @endif
        <!-- End of Purchase Order File -->
      @endif
      <!-- --------------------------- -->
      <!-- End of Purchase Order Links -->
      <!-- --------------------------- -->

<!-- --------------------------------------------------------------------- -->
      
      <!-- --------------------------- -->
      <!-- Start of Message Cast Links -->
      <!-- --------------------------- -->
      @if (\Auth::user()->hasAnyPermission([
        'Compose Messages',
        'Edit Message Cast Settings',

        'Show Contact Lists',
        'Create Contact Lists',
        'Edit Contact Lists',
        'Delete Contact Lists',
      ]))
        <li class="header">MESSAGE CAST</li>
        <!-- Start of Compose Message Links -->
        @if (\Auth::user()->hasAnyPermission([
          'Compose Messages',
        ]))
          <li class="{{ isset($is_mc_message_route) ? 'active' : '' }}">
            <a href="{{ route('messages.message_casts') }}">
              <i class="fa fa-envelope"></i> <span>Compose Message</span>
            </a>
          </li>
        @endif
        <!-- End of Compose Message Links -->
        <!-- Start of Contact List Links -->
        @if (\Auth::user()->hasAnyPermission([
          'Show Contact Lists',
          'Create Contact Lists',
          'Edit Contact Lists',
          'Delete Contact Lists',
        ]))
          <li class="{{ isset($is_mc_contact_list_route) ? 'active' : '' }}">
            <a href="{{ route('contact_lists.message_casts') }}">
              <i class="fa fa-address-book"></i> <span>Contact List</span>
            </a>
          </li>
        @endif
        <!-- End of Contact List Links -->
        <!-- Start of Setting Links -->
        @if (\Auth::user()->hasAnyPermission([
          'Edit Message Cast Settings',
        ]))
          <li class="{{ isset($is_mc_setting_route) ? 'active' : '' }}">
            <a href="{{ route('settings.message_casts') }}">
              <i class="fa fa-gears"></i> <span>Settings</span>
            </a>
          </li>
        @endif
      @endif
      <!-- End of Setting Links -->
      
      <!-- ------------------------- -->
      <!-- End of Message Cast Links -->
      <!-- ------------------------- -->

<!-- --------------------------------------------------------------------- -->
      
      <!-- ----------------------------- -->
      <!-- Start of Customer Links -->
      <!-- ----------------------------- -->
      @if (\Auth::user()->hasAnyPermission([
        'Show Customers',
        'Create Customers',
        'Edit Customers',
        'Delete Customers',
        'Import Customers',

        'Show Customer Files',
        'Create Customer Files',
        'Edit Customer Files',
        'Delete Customer Files',
      ]))
        <li class="header">CUSTOMER</li>
        <!-- Start of Take Photo Links -->
        @if (\Auth::user()->hasAnyPermission([
          'Create Customers',
        ]))
          <li class="{{ isset($is_customer_photo_route) && \Request::is('customers/basic') ? 'active' : '' }}">
            <a href="{{ route('customer.basic') }}">
              <i class="fa fa-camera"></i> <span>Take a Photo</span>
            </a>
          </li>
        @endif
        <!-- End of Take Photo Links -->
        <!-- Start of Customer Lists Links -->
        @if (\Auth::user()->hasAnyPermission([
          'Show Customers',
          'Create Customers',
          'Edit Customers',
          'Delete Customers',
          'Import Customers',
        ]))
          <li class="{{ isset($is_customer_photo_route) && !\Request::is('customers/basic') ? 'active' : '' }}">
            <a href="{{ route('customers') }}">
              <i class="fa fa-file-text"></i> <span>Customer Lists</span>
            </a>
          </li>
        @endif
        <!-- End of Customer Lists Links -->
        <!-- Start of Sync Links -->
        <!-- @if (\Auth::user()->hasAnyPermission([
          'Show Customer Files',
          'Create Customer Files',
          'Edit Customer Files',
          'Delete Customer Files',
        ]))
          <li class="{{ isset($is_customer_photo_route) && \Request::route()->getName() === 'customer.sync.index' ? 'active' : '' }}">
            <a href="{{ route('customer.sync.index') }}">
              <i class="fa fa-undo"></i> <span>Sync Data</span>
            </a>
          </li>
        @endif -->
        <!-- End of Sync Links -->
      @endif
      <!-- --------------------------- -->
      <!-- End of Customer Photo Links -->
      <!-- --------------------------- -->

<!-- --------------------------------------------------------------------- -->
      
      <!-- ---------------------- -->
      <!-- Start of Pending Links -->
      <!-- ---------------------- -->
      @if (\Auth::user()->hasAnyPermission([
        'Show Pendings',
        'Create Pendings',
        'Edit Pendings',
        'Delete Pendings',
        'Access Pending Charts',
        'Show Pending Charts',
      ]))
        <li class="header">PENDING MONITORING</li>
        <!-- Start of Pending Links -->
        <li class="{{ isset($is_pending_route) ? 'active' : '' }}">
          <a href="{{ route('pendings') }}">
            <i class="fa fa-commenting"></i> <span>Pending</span>
          </a>
        </li>
        <!-- End of Pending Links -->
      @endif
      <!-- -------------------- -->
      <!-- End of Pending Links -->
      <!-- -------------------- -->

<!-- --------------------------------------------------------------------- -->

      <!-- ----------------------- -->
      <!-- Start of Employee Links -->
      <!-- ----------------------- -->
      @if (\Auth::user()->hasAnyPermission([
        'Show Employees',
        'Edit Employees',
      ]))
        <li class="header">EMPLOYEE</li>
        <!-- Start of Employee Links -->
        <li class="{{ isset($is_employee_route) ? 'active' : '' }}">
          <a href="{{ route('employees.index') }}">
            <i class="fa fa-user-md"></i> <span>Employee</span>
          </a>
        </li>
        <!-- End of Employee Links -->
      @endif
      <!-- --------------------- -->
      <!-- End of Employee Links -->
      <!-- --------------------- -->

<!-- --------------------------------------------------------------------- -->

      <!-- ------------------------- -->
      <!-- Start of Scheduling Links -->
      <!-- ------------------------- -->
      @if (\Auth::user()->hasAnyPermission([
        'Show Interview Schedules',
        'Create Interview Schedules',
        'Edit Interview Schedules',
        'Delete Interview Schedules',

        'Add Interview Schedules',
      ]))
        <li class="header">SCHEDULING</li>
        <!-- Start of Interview Links -->
        <li class="{{ isset($is_interview_route) ? 'active' : '' }}">
          <a href="{{ route('interview_scheds.index') }}">
            <i class="fa fa-calendar-check-o"></i> <span>Interview</span>
          </a>
        </li>
        <!-- End of Interview Links -->
      @endif
      <!-- ----------------------- -->
      <!-- End of Scheduling Links -->
      <!-- ----------------------- -->

<!-- --------------------------------------------------------------------- -->

      <!-- -------------------------- -->
      <!-- Start of Engineering Links -->
      <!-- -------------------------- -->
      @if (\Auth::user()->hasAnyPermission([
        'Show Maintenance Requests',
        'Create Maintenance Requests',
        'Edit Maintenance Requests',
        'Delete Maintenance Requests',

        'Maintenance Request Lists',
        'Escalated Delete Maintenance Requests',

        'View Maintenance Requests',
        'Approve Maintenance Requests',
        'Cancel Maintenance Requests',

        'Overlook Maintenance Requests',
      ]))
        <li class="header">ENGINEERING</li>
        <!-- Start of Maintenance Links -->
        <li class="treeview {{ isset($is_maint_request_route) ? 'active' : (isset($is_mrf_approval_route) ? 'active' : '') }}">
          <a href="#"><i class="fa fa-wpforms"></i> <span>Maintenance Request</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            @if (\Auth::user()->hasAnyPermission([
              'Show Maintenance Requests',
              'Create Maintenance Requests',
              'Edit Maintenance Requests',
              'Delete Maintenance Requests',
            ]))
              <li class="{{ isset($is_maint_request_route) ? 'active' : '' }}">
                <a href="{{ route('maint_requests') }}"><i class="fa fa-circle-o"></i>&nbsp;Filing</a>
              </li>
            @endif
            @if (!\Auth::user()->hasAnyPermission([
                   'Overlook Maintenance Requests',
                 ]) &&
              \Auth::user()->hasAnyPermission([
              'Maintenance Request Lists',
              'Escalated Delete Maintenance Requests',

              'View Maintenance Requests',
              'Approve Maintenance Requests',
              'Cancel Maintenance Requests',
            ]))
              <li class="{{ \Request::route()->getName() === 'maint_request.approved_mrfs' || \Request::route()->getName() === 'maint_request.view_approved' ? 'active' : '' }}">
                <a href="{{ route('maint_request.approved_mrfs') }}"><i class="fa fa-circle-o"></i>&nbsp;Approved MRFs</a>
              </li>
              <li class="{{ isset($is_mrf_approval_route) || \Request::route()->getName() === 'maint_request.view' ? 'active' : '' }}">
                <a href="{{ route('maint_request.approval.pending') }}"><i class="fa fa-circle-o"></i>&nbsp;Approvals</a>
              </li>
            @endif
            @if (\Auth::user()->hasAnyPermission([
              'Overlook Maintenance Requests',
            ]))
              <li class="{{ \Request::route()->getName() === 'maint_request.approved_mrfs' || \Request::route()->getName() === 'maint_request.view_approved' ? 'active' : '' }}">
                <a href="{{ route('maint_request.approved_mrfs') }}"><i class="fa fa-circle-o"></i>&nbsp;Approved MRFs</a>
              </li>
              <li class="{{ \Request::route()->getName() === 'maint_request.approval.overlook' || \Request::route()->getName() === 'maint_request.view' ? 'active' : '' }}">
                <a href="{{ route('maint_request.approval.overlook') }}"><i class="fa fa-circle-o"></i>&nbsp;Overlooks</a>
              </li>
            @endif
          </ul>
        </li>
        <!-- End of Maintenance Links -->
      @endif
      <!-- ------------------------ -->
      <!-- End of Engineering Links -->
      <!-- ------------------------ -->

<!-- --------------------------------------------------------------------- -->

      <!-- --------------------------- -->
      <!-- Start of Announcement Links -->
      <!-- --------------------------- -->
      @if (\Auth::user()->hasAnyPermission([
        'Show Announcements',
        'Create Announcements',
        'Edit Announcements',
        'Delete Announcements',
        'View Announcements',
      ]))
        <li class="header">ANNOUNCEMENT</li>
        <li class="treeview {{ isset($is_announcement_route) ? 'active' : '' }}">
          <a href="#"><i class="fa fa-bullhorn"></i> <span>Announcements</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            @if (\Auth::user()->hasAnyPermission([
              'Show Announcements',
              'Create Announcements',
              'Edit Announcements',
              'Delete Announcements',
            ]))
              <li class="{{ isset($is_announcement_route) ? 'active' : '' }}">
                <a href="{{ route('announcements.index') }}"><i class="fa fa-circle-o"></i>&nbsp;Create</a>
              </li>
            @endif

            @if (\Auth::user()->hasAnyPermission([
              'View Announcements',
            ]))
              <li class="{{ isset($is_maint_request_route) || \Request::route()->getName() === 'announcements.view' ? 'active' : '' }}">
                <a href="{{ route('announcements.view') }}"><i class="fa fa-circle-o"></i>&nbsp;View</a>
              </li>
            @endif
          </ul>
        </li>
      @endif
      <!-- ------------------------- -->
      <!-- End of Announcement Links -->
      <!-- ------------------------- -->

<!-- --------------------------------------------------------------------- -->

      <!-- ------------------------- -->
      <!-- Start of Web Portal Links -->
      <!-- ------------------------- -->
      <li class="header">WEBPORTAL VERSION 8.0</li>
      <!-- Start of Interview Links -->
      <li>
        <a href="javascript:void(0);">
          <i class="fa fa-book"></i> <span>Documentation</span>
        </a>
      </li>
      <li>
        <a href="javascript:void(0);">
          <i class="fa fa-file-pdf-o"></i> <span>Download PDF</span>
        </a>
      </li>
      <!-- End of Interview Links -->
      <!-- ----------------------- -->
      <!-- End of Web Portal Links -->
      <!-- ----------------------- -->
    </ul>
  </section>
</aside>