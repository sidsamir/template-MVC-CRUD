<div class="container mt-5">
    <h2>Se connecter</h2>
    <form action="" method="post">

        <!-- on inclut le template partiel formErrors.tpl.php pour afficher les erreurs éventuelles -->
        <?php include __DIR__ . '/../partials/formErrors.tpl.php'; ?>

        <div>
            <label for="email" class="form-label">Adresse email</label>
            <input type="email" name="email" id="email" class="form-control">
        </div>
        <div>
            <label for="passowrd" class="form-label">Mot de passe</label>
            <input type="password" name="password" id="password" class="form-control">
        </div>

        <div>
            <button type="submit" class="btn btn-primary">Se connecter</button>
        </div>

    </form>
</div>