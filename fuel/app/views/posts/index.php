<?php foreach($posts as $post) { ?>
    <div class="blog-post">
        <h2 class="blog-post-title"><?php echo $post->title; ?></h2>
        <p class="blog-post-meta"><?php echo 'Tags: '. $post->tags; ?> <?php echo '. Category: '. $post->category; ?></p>
        <p class="blog-post-meta"><?php echo $post->created_date; ?></p>
        <p><?php echo Str::truncate($post->body, 300); ?>
        <br></br>
        <a class="btn btn-primary" href="/posts/view/<?php echo $post->id; ?>" role="button">View more</a>
    </div>
<?php } ?>

