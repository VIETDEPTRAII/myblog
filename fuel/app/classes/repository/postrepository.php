<?php
class Repository_PostRepository
{
    public function validate_inputs()
    {
        $validation = Validation::forge('my_validation');
        $validation->add('title', 'title', array(), array('trim', 'strip_tags'))
                    ->add_rule('required')
                    ->add_rule('min_length', 3);
        $validation->add('category', 'category', array(), array('trim', 'strip_tags'))
                    ->add_rule('required')
                    ->add_rule('min_length', 3);
        $validation->add('body', 'body', array(), array('trim', 'strip_tags'))
                    ->add_rule('required')
                    ->add_rule('min_length', 3);
        $validation->add('tags', 'tags', array(), array('trim', 'strip_tags'))
                    ->add_rule('required')
                    ->add_rule('min_length', 3);
        return $validation;
    }

    public function get_all_posts($offset, $limit)
    {
        $total_of_posts = Model_Posts::query()->count();
        $posts = Model_Posts::query()->where('deleted_date', null)
            ->order_by('created_date', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();
        $data = array($total_of_posts, $posts);
        return $data;
    }

    public function get_post_detail($id)
    {
        $post = Model_Posts::query()
            ->where(array('id' => $id, 'deleted_date' => null))
            ->get_one();
        return $post;
    }

    public function post_new_post($title, $category, $body, $tags)
    {
        $post = new Model_Posts();
        $post->title = $title;
        $post->category = $category;
        $post->body = $body;
        $post->tags = $tags;
        $post->created_date = date('Y-m-d H:i:s');
        $post->save();
        return $post;
    }

    public function put_post($id, $title, $category, $body, $tags)
    {
        $post = $this->get_post_detail($id);
        $post->set(array(
            'title' => $title,
            'category' => $category,
            'body' => $body,
            'tags' => $tags,
            'updated_date' => date('Y-m-d H:i:s'),
        ));
        $post->save();
        return $post;
    }

    public function delete_post($id)
    {
        $post = Model_Posts::query()
            ->where(array('id' => $id, 'deleted_date' => null))
            ->get_one();
        if ($post === null)
        {
            return null;
        }
        else
        {
            $post->delete();
            return true;
        }
    }
}
