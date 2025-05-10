<?php

return [
    /**
     * Use uuid as primary key.
     */
    'uuids' => false,

    /*
     * User tables foreign key name.
     */
    'user_foreign_key' => 'user_id',
	
    /*
     * Anime tables foreign key name.
     */
    'anime_foreign_key' => 'anime_id',		

    /*
     * Table name for watchings records.
     */
    'watchings_table' => 'watchings',

    /*
     * Model name for watching record.
     */
    'watching_model' => Animelhd\AnimesWatching\Watching::class,

	/*
     * Model name for watchingable record.
     */
    'watchingable_model' => App\Models\Anime::class,

     /*
     * Model name for watchinger model.
     */
    'watchinger_model' => App\Models\User::class,
];
