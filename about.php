<?php
/**
 * Forge Frame Studios — About
 * Company story, mission, team.
 */
define('FORGEFRAME', true);
require_once __DIR__ . '/includes/config.php';
$current_page = 'about';
$page_title = 'About Us';
$meta_description = 'Learn about Forge Frame Studios, a media and design company specializing in cinematic brand films and documentary storytelling.';
require_once __DIR__ . '/includes/header.php';
?>

<section class="section page-hero-inner">
    <div class="container">
        <h1 class="page-title">About Forge Frame Studios</h1>
        <p class="lead text-muted">We are a media and design studio delivering high-end video production for brands and creators.</p>
    </div>
</section>

<section class="section" aria-labelledby="story-heading">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <h2 id="story-heading" class="section-title">Our Story</h2>
                <p>Forge Frame Studios started with a simple belief: every brand and creator has a story worth telling in a way that feels cinematic and authentic. We built our team around that idea, bringing together directors, editors, colorists, and motion designers who share a common language of light, movement, and sound.</p>
                <p>Today we work across commercials, corporate films, events, and long-form content. From the first concept to the final export, we focus on clarity, collaboration, and a finish that stands up on any screen.</p>
            </div>
            <div class="col-lg-6">
                <div class="about-image-wrap">
                    <img src="<?php echo htmlspecialchars(get_image_src('about-story.jpg')); ?>" alt="Forge Frame Studios team at work" loading="lazy" class="img-fluid rounded-3">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section bg-charcoal-soft" aria-labelledby="mission-heading">
    <div class="container">
        <h2 id="mission-heading" class="section-title text-center">Our Mission</h2>
        <p class="mission-statement text-center mx-auto">To craft cinematic stories that connect brands and creators with their audiences—through clarity, craft, and a commitment to every frame.</p>
    </div>
</section>

<section class="section" aria-labelledby="team-heading">
    <div class="container">
        <h2 id="team-heading" class="section-title text-center">The Team</h2>
        <p class="text-center text-muted mb-5">The people behind the lens and the timeline.</p>
        <div class="row g-4 justify-content-center">
            <div class="col-sm-6 col-lg-4">
                <div class="team-card card h-100">
                    <div class="team-avatar" aria-hidden="true"><span class="team-initial">M</span></div>
                    <div class="card-body text-center">
                        <h3 class="team-member-name">Mary Smith</h3>
                        <p class="team-member-role">Lead Director</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="team-card card h-100">
                    <div class="team-avatar" aria-hidden="true"><span class="team-initial">J</span></div>
                    <div class="card-body text-center">
                        <h3 class="team-member-name">John Wang</h3>
                        <p class="team-member-role">Head of Post</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="team-card card h-100">
                    <div class="team-avatar" aria-hidden="true"><span class="team-initial">A</span></div>
                    <div class="card-body text-center">
                        <h3 class="team-member-name">Alex Bington</h3>
                        <p class="team-member-role">Motion & Design</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="team-card card h-100">
                    <div class="team-avatar" aria-hidden="true"><span class="team-initial">P</span></div>
                    <div class="card-body text-center">
                        <h3 class="team-member-name">Priya Rao</h3>
                        <p class="team-member-role">Producer</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="team-card card h-100">
                    <div class="team-avatar" aria-hidden="true"><span class="team-initial">O</span></div>
                    <div class="card-body text-center">
                        <h3 class="team-member-name">Omar Khalid</h3>
                        <p class="team-member-role">DP & Drone</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section cta-section" aria-labelledby="about-cta-heading">
    <div class="container text-center">
        <h2 id="about-cta-heading" class="section-title text-white">Let's work together</h2>
        <a href="/contact.php" class="btn btn-primary btn-cta-fill">Get in Touch</a>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
