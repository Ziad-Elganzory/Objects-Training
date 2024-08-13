<?php

use Kreait\Firebase\Messaging\Topic;

return [
    'posts'=>[
        'success'=>[
            'posts_retrived'=>'Posts Retrived Successfully',
            'post_created'=>'Post Created Successfully',
            'post_retrived'=>'Post Retrived Successfully',
            'post_update'=>'Post Updated Successfully',
            'post_delete'=>'Post Deleted Successfully',
        ],
        'fail'=>[
            'posts_retrived'=>'Failed to retrive posts',
            'post_created'=>'Failed to creat post',
            'post_retrived'=>'Failed to retrive post',
            'post_update'=>'Failed to update post',
            'post_delete'=>'Failed to delete post',
        ],
    ],
    'firebase_notification' => [
        'success'=>[
            'topic'=>'Notification sent to topic successfully',
            'singleDevice'=>'Notification sent to Device successfully'
        ],
        'fail'=>[
            'topic'=>'Failed to send notification to topic',
            'singleDevice'=>'Failed to send notification to Device',
        ],
    ],

];
