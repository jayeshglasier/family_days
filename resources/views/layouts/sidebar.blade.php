<div class="navbar-default sidebar" role="navigation" style="background-color:orange !important;min-height: 100%;">
    <div class="sidebar-nav navbar-collapse">
        <ul class="nav" id="side-menu" style="box-shadow: 0px 0px 7px #a79d9d;">
          <li><a href="{{ url('/home') }}" style="color:#fff;"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a></li>
          <li><a href="{{ url('/users') }}" style="color:#fff;"><i class="fa fa-user"></i> Users</a></li>
          <li><a href="{{ url('/familys') }}" style="color:#fff;"><i class="fa fa-users"></i> Family</a></li>
          <li><a href="#" style="color:#fff;"><i class="fa fa-tachometer"></i> Chores<span class="fa arrow"></a>
              <ul class="nav child_menu">
                  <li><a href="{{ url('/chores') }}" style="color:#fff;">&nbsp&nbsp&nbsp <i class="fa fa-tachometer"></i> Chores</a></li>
                  <li><a href="{{ url('/preset-chores') }}" style="color:#fff;">&nbsp&nbsp&nbsp <i class="fa fa-tachometer"></i> Preset Chores</a></li>
              </ul>
          </li>
          <li><a href="#" style="color:#fff;"><i class="fa fa-futbol-o" aria-hidden="true"></i> Reward<span class="fa arrow"></a>
                <ul class="nav child_menu">
                   <li><a href="{{ url('/reward-list') }}" style="color:#fff;">&nbsp&nbsp&nbsp <i class="fa fa-futbol-o" aria-hidden="true"></i> Reward</a></li>
                    <li><a href="{{ url('/reward-category') }}" style="color:#fff;">&nbsp&nbsp&nbsp <i class="fa fa-futbol-o" aria-hidden="true"></i> Category</a></li>
                    <li><a href="{{ url('/reward-brand') }}" style="color:#fff;">&nbsp&nbsp&nbsp <i class="fa fa-futbol-o" aria-hidden="true"></i> Brand</a></li>
                    <li><a href="{{ url('/products') }}" style="color:#fff;">&nbsp&nbsp&nbsp <i class="fa fa-futbol-o" aria-hidden="true"></i> Products</a></li>
                </ul>
            </li>
          <li><a href="{{ url('/claims') }}" style="color:#fff;"><i class="fa fa-gift" aria-hidden="true"></i> Claims</a></li>
          <li><a href="{{ url('/help') }}" style="color:#fff;"><i class="fa fa-question" aria-hidden="true"></i> Help</a></li>
          <!-- <li><a href="{{ url('/message') }}" style="color:#fff;"><i class="fa fa-envelope"></i> Message</a></li> -->
          <li><a href="{{ url('/setting') }}" style="color:#fff;"><i class="fa fa-cogs"></i> Setting</a></li>           
        </ul>
    </div>
</div>