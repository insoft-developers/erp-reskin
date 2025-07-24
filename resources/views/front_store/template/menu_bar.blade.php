<div class="menubar-area style-7 footer-fixed rounded-0">
    <div class="toolbar-inner menubar-nav">
        <a href="/front-store" class="nav-link {{ request()->is('front-store') ? 'active' : '' }}">
            <i class="fa-solid fa-house"></i>
            <span>Home</span>
        </a>
        <a href="/kategori" class="nav-link {{ request()->is('kategori') ? 'active' : '' }}">
            <i class="fa-solid fa-box"></i>
            <span>Categories</span>
        </a>
        <a href="favorite.html" class="nav-link {{ request()->is('favorites') ? 'active' : '' }}">
            <i class="fa-solid fa-heart"></i>
            <span>Favorites</span>
        </a>
        <a href="cart.html" class="nav-link">
            <i class="fa-solid fa-bag-shopping"></i>
            <span>Cart</span>
        </a>
        <a href="account.html" class="nav-link">
            <i class="fa-solid fa-user"></i>
            <span>Account</span>
        </a>
    </div>
</div>
