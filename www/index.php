<?php
// Start session first!
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<?php include __DIR__ . '/includes/header.php'; ?>
<main>

  <!-- Hero -->
  <section class="hero">
    <div class="overlay">
      <h1>Find your next great read</h1>
      <p>Warm shelves. Wise stories. A cozy corner of the web.</p>
      <div class="hero-cta">
        <a class="btn btn-primary" href="/search.php?q=new">Shop New Arrivals</a>
        <a class="btn btn-ghost" href="/review.php">See Community Reviews</a>
      </div>
    </div>
  </section>

  <!-- Featured Categories -->
  <section class="section container">
    <div class="section-head">
      <h2>Browse by Category</h2>
      <p class="sub">Classic genres with a modern touch</p>
    </div>
    <div class="grid categories">
      <a class="tile" href="/search.php?q=Fiction">
        <img src="/assets/img/cat-fiction.jpg" alt="Fiction">
        <span>Fiction</span>
      </a>
      <a class="tile" href="/search.php?q=Non-fiction">
        <img src="/assets/img/cat-nonfiction.jpg" alt="Non-fiction">
        <span>Non-fiction</span>
      </a>
      <a class="tile" href="/search.php?q=Children">
        <img src="/assets/img/cat-children.jpg" alt="Children’s">
        <span>Children’s</span>
      </a>
      <a class="tile" href="/search.php?q=Staff%20Picks">
        <img src="/assets/img/cat-staffpicks.jpg" alt="Staff Picks">
        <span>Staff Picks</span>
      </a>
      <a class="tile" href="/search.php?q=Local">
        <img src="/assets/img/cat-local.jpg" alt="Local Authors">
        <span>Local Authors</span>
      </a>
    </div>
  </section>

  <!-- Featured Books (static demo cards) -->
  <section class="section alt">
    <div class="container">
      <div class="section-head">
        <h2>Featured Books</h2>
        <p class="sub">Hand-picked by our team</p>
      </div>
      <div class="grid books">
        <?php
        $books = [
          [
            'title' => 'All the Colors of the Dark',
            'author' => 'Chris Whitaker',
            'image' => 'All the Colors of the Dark.jpg',
            'price' => 10.99
          ],
          [
            'title' => 'Dune',
            'author' => 'Frank Herbert',
            'image' => 'dune.jpg',
            'price' => 11.99
          ],
          [
            'title' => 'Pride and Prejudice',
            'author' => 'Jane Austen',
            'image' => 'Pride and Prejudice.jpg',
            'price' => 12.99
          ],
          [
            'title' => 'Prisoner\'s Dilemma',
            'author' => 'Richard Powers',
            'image' => 'Prisoner\'s Dilemma.jpg',
            'price' => 13.99
          ],
          [
            'title' => 'Sapiens',
            'author' => 'Yuval Noah Harari',
            'image' => 'Sapiens.jpg',
            'price' => 14.99
          ],
          [
            'title' => 'The Girl with the Dragon Tattoo',
            'author' => 'Stieg Larsson',
            'image' => 'The Girl with the Dragon Tattoo.jpg',
            'price' => 15.99
          ],
          [
            'title' => 'The Godfather',
            'author' => 'Mario Puzo',
            'image' => 'The Godfather.jpg',
            'price' => 16.99
          ],
          [
            'title' => 'The Great Gatsby',
            'author' => 'F. Scott Fitzgerald',
            'image' => 'The Great Gatsby.jpg',
            'price' => 17.99
          ]

            ,
            [
              'title' => 'A Tale of Two Cities',
              'author' => 'Charles Dickens',
              'image' => 'A Tale of Two Cities.jpg',
              'price' => 18.99
            ],
            [
              'title' => 'Don Quixote',
              'author' => 'Miguel de Cervantes',
              'image' => 'Don Quixote.jpg',
              'price' => 19.99
            ]
        ];
        foreach ($books as $book):
        ?>
        <article class="book-card">
          <img src="/assets/img/<?php echo $book['image']; ?>" alt="<?php echo $book['title']; ?>" class="cover">
          <h3 class="title"><?php echo $book['title']; ?></h3>
          <p class="author">by <?php echo $book['author']; ?></p>
          <div class="price-row">
            <span class="price">$<?php echo $book['price']; ?></span>
            <button class="btn btn-small">Quick View</button>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Community / Events -->
  <section class="section container">
    <div class="section-head">
      <h2>Community & Events</h2>
      <p class="sub">Join the conversation</p>
    </div>
    <div class="grid events">
      <article class="event">
        <h3>Author Spotlight: Mira Senanayake</h3>
        <p>Saturday 4pm · Readings & signing · Free entry</p>
      </article>
      <article class="event">
        <h3>Monthly Book Club</h3>
        <p>Last Thursday · Cozy classics · Tea & biscuits provided</p>
      </article>
      <article class="event">
        <h3>Newsletter</h3>
        <p>Get staff picks, local authors, and quiet sales.</p>
        <form class="newsletter-inline" onsubmit="alert('Thanks! (demo)'); return false;">
          <input type="email" placeholder="you@example.com" required>
          <button class="btn btn-small">Subscribe</button>
        </form>
      </article>
    </div>
  </section>

</main>
<?php include __DIR__ . '/includes/footer.php'; ?>