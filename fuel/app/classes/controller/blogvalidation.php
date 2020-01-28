<?php
class Controller_BlogValidation
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
}
