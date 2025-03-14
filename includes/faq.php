<section class="faq-section">
    <div class="container">
        <h2 class="section-title">Foire Aux Questions</h2>
        <div class="faq-container">
            <?php
            $faqs = [
                [
                    "question" => "Comment acheter une voiture sur votre site ?",
                    "answer" => "Acheter une voiture sur notre site est simple et rapide. Il vous suffit de parcourir notre catalogue, d'utiliser les filtres pour affiner votre recherche, puis de sélectionner le véhicule qui vous intéresse. Une fois votre choix fait, suivez les instructions pour finaliser votre commande et effectuer le paiement en toute sécurité."
                ],
                [
                    "question" => "Proposez-vous des options de financement ou de location ?",
                    "answer" => "Oui, nous proposons des solutions de financement adaptées à vos besoins, notamment des paiements en plusieurs fois et des crédits auto en partenariat avec des établissements financiers. Nous offrons également des options de location longue durée (LLD) et de location avec option d'achat (LOA)."
                ],
                [
                    "question" => "Quels sont les modes de paiement acceptés ?",
                    "answer" => "Nous acceptons plusieurs modes de paiement pour faciliter vos achats : cartes bancaires, virements bancaires sécurisés, et solutions de paiement en ligne comme Stripe. Toutes les transactions sont protégées pour garantir votre sécurité."
                ],
                [
                    "question" => "Est-ce possible de localiser mon véhicule loué en cas de vol ?",
                    "answer" => "Oui, tous nos véhicules sont équipés d'un système de géolocalisation moderne. En cas de vol, nous pouvons immédiatement localiser le véhicule et travailler avec les autorités pour sa récupération rapide."
                ],
                [
                    "question" => "Qu'est-ce que l'Airbag Vert et quels sont ses avantages ?",
                    "answer" => "L'Airbag Vert est une innovation écologique qui combine sécurité et respect de l'environnement. Il utilise des matériaux biodégradables tout en garantissant une protection optimale. Ce système réduit significativement l'impact environnemental tout en maintenant les plus hauts standards de sécurité."
                ],
                [
                    "question" => "Comment se passe la livraison du véhicule ?",
                    "answer" => "Nous proposons plusieurs options de livraison : récupération dans nos locaux, livraison à domicile, ou dans un point relais partenaire. Chaque livraison inclut une inspection complète du véhicule et une explication détaillée des fonctionnalités."
                ]
            ];
            ?>

            <div class="faq-grid">
                <?php foreach ($faqs as $faq): ?>
                    <div class="faq-item" data-aos="fade-up" data-aos-duration="800">
                        <div class="faq-question">
                            <span class="question-text"><?= htmlspecialchars($faq['question']) ?></span>
                            <span class="faq-toggle">
                                <i class="fas fa-plus"></i>
                            </span>
                        </div>
                        <div class="faq-answer">
                            <p><?= htmlspecialchars($faq['answer']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="contact-section" data-aos="fade-up" data-aos-duration="1000">
            <h3>Vous avez d'autres questions ?</h3>
            <p>Notre équipe est là pour vous aider et répondre à toutes vos questions.</p>
            <a href="<?= url('contact') ?>" class="contact-btn">
                <i class="fas fa-envelope"></i>
                Contactez-nous
            </a>
        </div>
    </div>
</section>

<style>
.faq-section {
    padding: 5rem 0;
    background: linear-gradient(to bottom, var(--light) 0%, white 100%);
}

.section-title {
    text-align: center;
    font-size: 2.5rem;
    color: var(--primary-color);
    margin-bottom: 3rem;
    position: relative;
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: -15px;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 3px;
    background: var(--secondary-color);
}

.faq-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 0 1rem;
}

.faq-grid {
    display: grid;
    gap: 1.5rem;
}

.faq-item {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.faq-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.faq-question {
    padding: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    background: white;
    transition: all 0.3s ease;
}

.question-text {
    font-weight: 600;
    color: var(--primary-color);
    font-size: 1.1rem;
    flex: 1;
    padding-right: 1rem;
}

.faq-toggle {
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--light);
    border-radius: 50%;
    color: var(--primary-color);
    transition: all 0.3s ease;
}

.faq-item.active .faq-toggle {
    background: var(--secondary-color);
    color: white;
    transform: rotate(45deg);
}

.faq-answer {
    max-height: 0;
    overflow: hidden;
    transition: all 0.3s ease;
    background: var(--light);
}

.faq-item.active .faq-answer {
    max-height: 500px;
    padding: 1.5rem;
}

.faq-answer p {
    color: var(--text-color);
    line-height: 1.6;
    margin: 0;
}

.contact-section {
    text-align: center;
    margin-top: 4rem;
    padding: 2rem;
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
}

.contact-section h3 {
    color: var(--primary-color);
    font-size: 1.5rem;
    margin-bottom: 1rem;
}

.contact-section p {
    color: var(--text-color);
    margin-bottom: 1.5rem;
}

.contact-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem 2rem;
    background: var(--secondary-color);
    color: white;
    text-decoration: none;
    border-radius: 50px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.contact-btn:hover {
    background: var(--secondary-dark);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(var(--secondary-rgb), 0.3);
}

@media (max-width: 768px) {
    .section-title {
        font-size: 2rem;
    }

    .question-text {
        font-size: 1rem;
    }

    .faq-question {
        padding: 1.2rem;
    }

    .faq-answer {
        padding: 1.2rem;
    }

    .contact-section {
        margin-top: 3rem;
        padding: 1.5rem;
    }
}

@media (max-width: 480px) {
    .section-title {
        font-size: 1.8rem;
    }

    .contact-btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const faqItems = document.querySelectorAll('.faq-item');
    
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        
        question.addEventListener('click', () => {
            const isActive = item.classList.contains('active');
            
            // Ferme toutes les réponses
            faqItems.forEach(faq => {
                faq.classList.remove('active');
            });
            
            // Ouvre la réponse cliquée si elle n'était pas déjà ouverte
            if (!isActive) {
                item.classList.add('active');
            }
        });
    });
});
</script>