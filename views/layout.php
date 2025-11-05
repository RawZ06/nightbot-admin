<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nightbot Admin</title>
    <link rel="stylesheet" href="/static/style.css">
</head>
<body>
    <nav>
        <div class="nav-brand">
            <h1>Nightbot Admin</h1>
        </div>
        <div class="nav-tabs">
            <a href="/admin/commands" class="<?= ($page ?? '') === 'commands' ? 'active' : '' ?>">Commandes</a>
            <a href="/admin/quotes" class="<?= ($page ?? '') === 'quotes' ? 'active' : '' ?>">Quotes</a>
            <a href="/admin/help" class="<?= ($page ?? '') === 'help' ? 'active' : '' ?>">Aide</a>
        </div>
        <div class="nav-actions">
            <?php if (\App\Auth::check()): ?>
                <span class="user-name"><?= htmlspecialchars(\App\Auth::getUser()) ?></span>
                <a href="/logout" class="btn-logout">Déconnexion</a>
            <?php endif; ?>
        </div>
    </nav>

    <main>
        <?php require __DIR__ . '/' . $view . '.php'; ?>
    </main>
</body>
</html>
