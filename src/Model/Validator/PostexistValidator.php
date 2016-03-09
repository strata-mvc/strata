<?php

namespace Strata\Model\Validator;

class PostexistValidator extends Validator
{

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->setMessage(__("This post could not be found.", $this->getTextdomain()));
    }

    /**
     * {@inheritdoc}
     */
    public function test($value, $context)
    {
        $postId = (int)$value;

        if ($postId > 0) {
            // https://tommcfarlin.com/wordpress-post-exists-by-id/
            return is_string(get_post_status($postId));
        }

        return true;
    }
}
