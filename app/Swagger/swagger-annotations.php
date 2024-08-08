<?php

    

    /**
     * @OA\Schema(
     *     schema="Post",
     *     type="object",
     *     title="Post",
     *     required={"title", "content"},
     *     @OA\Property(
     *         property="id",
     *         type="integer",
     *         description="ID of the post"
     *     ),
     *     @OA\Property(
     *         property="title",
     *         type="string",
     *         description="Title of the post"
     *     ),
     *     @OA\Property(
     *         property="content",
     *         type="string",
     *         description="Content of the post"
     *     )
     * )
     */
