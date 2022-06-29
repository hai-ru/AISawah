<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
    <a class="navbar-brand" href="#">{{env("APP_NAME")}}</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link {{ Request::is('/') ? "active" : "" }}" href="/">Simple Additive Weighting (SAW)</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('maut') ? "active" : "" }}" href="{{ route('maut') }}">Multi Attribute Utility Theory (MAUT)</a>
            </li>
        </ul>
    </div>
</div>
</nav>