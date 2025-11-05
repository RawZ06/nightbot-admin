<div class="container">
    <div class="header">
        <h2>Commandes Nightbot</h2>
        <a href="/admin/command/new" class="btn btn-primary">Nouvelle commande</a>
    </div>

    <?php if (empty($commands)): ?>
        <p class="empty-state">Aucune commande créée pour le moment.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Créée le</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($commands as $command): ?>
                    <tr>
                        <td><code>!<?= htmlspecialchars($command->name) ?></code></td>
                        <td><?= htmlspecialchars($command->description ?? '-') ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($command->created_at)) ?></td>
                        <td>
                            <a href="/admin/command/<?= $command->id ?>" class="btn btn-small">Éditer</a>
                            <form method="POST" action="/admin/command/<?= $command->id ?>/delete" style="display: inline;">
                                <button type="submit" class="btn btn-small btn-danger" onclick="return confirm('Supprimer cette commande ?')">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
