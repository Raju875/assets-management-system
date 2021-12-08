<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!--Logo -->
    <a href="{{ route('dashboard') }}" class="brand-link">
        <img src="{{ asset('assets/img/logo.png') }}"
             class="brand-image img-circle elevation-3"
             style="height: 30px; width: 40px">
        <span class="brand-text font-weight-light">{{ config('app.name') }}</span>
    </a>

@php
    $role_id = \Illuminate\Support\Facades\Auth::user()->role_id
@endphp

<!-- Sidebar -->
    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                {{--Dashboard--}}
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}"
                       class="nav-link {{ ((Request::is('dashboard')) ? 'active' : '') }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                {{--Department--}}
                @if($role_id == 1)
                    <li class="nav-item">
                        <a href="{{ route('department-list') }}"
                           class="nav-link {{ ((Request::is('department/*')) ? 'active' : '') }}">
                            <i class="nav-icon fas fa-cube"></i>
                            <p>Department</p>
                        </a>
                    </li>
                @endif

                {{--User--}}
                @if(in_array($role_id, [1, 2]))
                    <li class="nav-item">
                        <a href="{{ route('user-list') }}"
                           class="nav-link {{ ((Request::is('user/*')) ? 'active' : '') }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>User</p>
                        </a>
                    </li>
                @endif

                {{--Asset--}}
                @if($role_id == 1)
                    <li class="nav-item has-treeview {{ ((Request::is('asset/*')) ? 'menu-open' : '') }}"
                        style="display: {{ ((Request::is('product/*')) ? 'block' : '') }}">
                        <a href="#" class="nav-link {{ ((Request::is('asset/*')) ? 'active' : '') }}">
                            <i class="nav-icon fas fa-th"></i>
                            <p>
                                Assets
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            {{--Category--}}
                            <li class="nav-item">
                                <a href="{{ route('asset-category-list') }}"
                                   class="nav-link {{ ((Request::is('asset/category-list')) ? 'active' : '') }}">
                                    <i class="nav-icon fas fa-cubes"></i>
                                    <p>Category</p>
                                </a>
                            </li>
                            {{--Asset--}}
                            <li class="nav-item">
                                <a href="{{ route('asset-list') }}"
                                   class="nav-link @if(Request::is('asset/list') || Request::is('asset/add') || Request::is('asset/edit/*')) active @endif">
                                    <i class="fas fa-list nav-icon"></i>
                                    <p>Asset</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                {{--Allocate--}}
                @if($role_id == 1)
                    <li class="nav-item">
                        <a href="{{ route('allocate-list') }}"
                           class="nav-link {{ ((Request::is('allocate/*')) ? 'active' : '') }}">
                            <i class="fas fa-box nav-icon "></i>
                            <p>Allocate</p>
                        </a>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
    <!-- /.sidebar -->
</aside>
