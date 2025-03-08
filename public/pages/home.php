<section class="popular-brands">
    <div class="container">
        <h2 class="section-title">Nos marques populaires</h2>
        <div class="brands-grid">
            <?php
            // Récupération des marques distinctes depuis la table vehicules
            $query = "SELECT DISTINCT marque FROM vehicules ORDER BY marque";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $marques = $stmt->fetchAll(PDO::FETCH_COLUMN);

            // Tableau associatif des logos de marques
            $logosPaths = [
                'Toyota' => 'toyota-logo.png',
                'BMW' => 'bmw-logo.png',
                'Mercedes' => 'mercedes-logo.png',
                'Audi' => 'audi-logo.png',
                'Tesla' => 'tesla-logo.png',
                'Porsche' => 'porsche-logo.png',
                'Ford' => 'ford-logo.png',
                'Volkswagen' => 'vw-logo.png'
                // Ajoutez d'autres marques selon vos besoins
            ];

            foreach ($marques as $marque) {
                $logoPath = isset($logosPaths[$marque]) 
                    ? asset('images/brands/' . $logosPaths[$marque])
                    : asset('images/brands/default-brand.png');
                ?>
                <a href="<?= url('catalogue/?marque=' . urlencode($marque)) ?>" class="brand-logo">
                    <img src="<?= $logoPath ?>" 
                         alt="Logo <?= htmlspecialchars($marque) ?>" 
                         title="Voir les véhicules <?= htmlspecialchars($marque) ?>">
                </a>
                <?php
            }
            ?>
        </div>
    </div>
</section> 