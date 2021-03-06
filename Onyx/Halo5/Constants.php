<?php

namespace Onyx\Halo5;

class Constants
{
    /**
     * Halo 5 Playlist history.
     *
     * @var string
     */
    public static $metadata_playlist = 'https://www.haloapi.com/metadata/h5/metadata/playlists';

    /**
     * Halo5 Medal explanation.
     *
     * @var string
     */
    public static $metadata_medals = 'https://www.haloapi.com/metadata/h5/metadata/medals';

    /**
     * CSR levels explained.
     *
     * @var string
     */
    public static $metadata_csr = 'https://www.haloapi.com/metadata/h5/metadata/csr-designations';

    /**
     * Seasons explained.
     *
     * @var string
     */
    public static $metadata_seasons = 'https://www.haloapi.com/metadata/h5/metadata/seasons';

    /**
     * Weapons explained.
     *
     * @var string
     */
    public static $metadata_weapons = 'https://www.haloapi.com/metadata/h5/metadata/weapons';

    /**
     * Game Base Variants explained.
     *
     * @var string
     */
    public static $metadata_gametypes = 'https://www.haloapi.com/metadata/h5/metadata/game-base-variants';

    /**
     * Halo5 Maps explained.
     *
     * @var string
     */
    public static $metadata_maps = 'https://www.haloapi.com/metadata/h5/metadata/maps';

    /**
     * Halo5 Ranks (Level 1 - 152).
     *
     * @var string
     */
    public static $metadata_ranks = 'https://www.haloapi.com/metadata/h5/metadata/spartan-ranks';

    /**
     * Halo5 Enemies.
     *
     * @var string
     */
    public static $metadata_enemies = 'https://www.haloapi.com/metadata/h5/metadata/enemies';

    /**
     * Halo5 Impulses.
     *
     * @var string
     */
    public static $metadata_impulses = 'https://www.haloapi.com/metadata/h5/metadata/impulses';

    /**
     * Halo5 Teams (Team Red - Magenta).
     *
     * @var string
     */
    public static $metadata_teams = 'https://www.haloapi.com/metadata/h5/metadata/team-colors';

    /**
     * Halo5 Vehicles.
     *
     * @var string
     */
    public static $metadata_vehicles = 'https://www.haloapi.com/metadata/h5/metadata/vehicles';

    /**
     * Halo 5 Map Variants.
     *
     * @var string
     */
    public static $metadata_mapvariants = 'https://www.haloapi.com/metadata/h5/metadata/map-variants/%s';

    /**
     * Halo5 Arena stats, all-time.
     *
     * @var string
     */
    public static $servicerecord_arena = 'https://www.haloapi.com/stats/h5/servicerecords/arena?players=%s';

    /**
     * Halo5 Warzone stats, all-time.
     *
     * @var string
     */
    public static $servicerecord_warzone = 'https://www.haloapi.com/stats/h5/servicerecords/warzone?players=%s';

    /**
     * Halo5 PostGameCarnage Report.
     *
     * 1: type (arena/warzone)
     * 2:
     *
     * @var string
     */
    public static $postgame_carnage = 'https://www.haloapi.com/stats/h5/%1$s/matches/%2$s';

    /**
     * Halo5 Matches for a specified player.
     * 1: gamertag
     * 2: modes
     * 3: start
     * 4: count.
     *
     * @var string
     */
    public static $player_matches = 'https://www.haloapi.com/stats/h5/players/%1$s/matches?modes=%2$s&start=%3$u&count=%4$u';

    /**
     * Halo5 Event stats of a match. MatchID required.
     *
     * @var string
     */
    public static $match_events = 'https://www.haloapi.com/stats/h5/matches/%s/events';

    /**
     * Halo5 Lederboard of a playlist. SeasonId and PlaylistId required.
     *
     * @var string
     */
    public static $leaderboard = 'https://www.haloapi.com/stats/h5/player-leaderboards/csr/%1$s/%2$s?count=250';

    /**
     * Halo5 Emblem URL.
     *
     * @var string
     */
    public static $emblem_image = 'https://www.haloapi.com/profile/h5/profiles/%1$s/emblem?size=%2$d';

    /**
     * Halo5 Spartan Image URL.
     *
     * @var string
     */
    public static $spartan_image = 'https://www.haloapi.com/profile/h5/profiles/%1$s/spartan?size=%2$d';
}
