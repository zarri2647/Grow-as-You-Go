<?php

/**
 * Class Database
 * Handles the connection and initial setup of the SQLite database.
 */
class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            try {
                self::$instance = new PDO('sqlite:leaderboard.db');
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::initializeTable();
            } catch (PDOException $e) {
                die("Database Connection Error: " . $e->getMessage());
            }
        }
        return self::$instance;
    }

    private static function initializeTable(): void {
        $db = self::$instance;
        $db->exec("CREATE TABLE IF NOT EXISTS players (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            score INTEGER NOT NULL,
            initials TEXT NOT NULL,
            avatar_color TEXT DEFAULT '#e9ecef',
            text_color TEXT DEFAULT '#495057'
        )");

        // Seed if empty
        $count = $db->query("SELECT COUNT(*) FROM players")->fetchColumn();
        if ($count == 0) {
            $stmt = $db->prepare("INSERT INTO players (name, score, initials, avatar_color, text_color) VALUES (?, ?, ?, ?, ?)");
            $dummyPlayers = [
                ['Alex Adams', 2450, 'AA', '#ffe3e3', '#ff6b6b'],
                ['Chloe Miller', 2120, 'CM', '#e3fafc', '#1098ad'],
                ['David Brown', 1980, 'DB', '#f3f0ff', '#845ef7'],
                ['Emma Watson', 1850, 'EW', '#ebfbee', '#40c057'],
                ['James Smith', 1500, 'JS', '#fff4e6', '#fd7e14']
            ];
            foreach ($dummyPlayers as $player) {
                $stmt->execute($player);
            }
        }
    }
}

/**
 * Class Player
 * Represents a single Player entity in the application.
 */
class Player {
    public string $name;
    public int $score;
    public string $initials;
    public string $avatarColor;
    public string $textColor;

    public function __construct(array $data) {
        $this->name = $data['name'] ?? 'Unknown';
        $this->score = (int)($data['score'] ?? 0);
        $this->initials = $data['initials'] ?? '??';
        $this->avatarColor = $data['avatar_color'] ?? '#e9ecef';
        $this->textColor = $data['text_color'] ?? '#495057';
    }

    public function getFormattedScore(): string {
        return number_format($this->score) . " pts";
    }

    public function getNameEscaped(): string {
        return htmlspecialchars($this->name, ENT_QUOTES, 'UTF-8');
    }
}

/**
 * Class Leaderboard
 * Coordinates fetching data and mapping rows into Player objects.
 */
class Leaderboard {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * @return Player[]
     */
    public function getTopPlayers(int $limit = 10): array {
        $stmt = $this->db->prepare("SELECT * FROM players ORDER BY score DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        $players = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $players[] = new Player($row);
        }
        return $players;
    }
}

// Instantiate Controller & Fetch Data
$leaderboard = new Leaderboard();
$topPlayers = $leaderboard->getTopPlayers(5);
?>
<!doctype html>
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <style type="text/css" media="all">
            * { box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
            body { background-color: #f4f6f9; margin: 0; padding: 20px; display: flex; justify-content: center; }
            .container { margin-top: 50px; border-radius: 12px; background-color: #ffffff; width: 100%; max-width: 450px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); overflow: hidden; }
            .leaderboard_background { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px 20px; text-align: center; color: white; }
            .leaderboard_background h2 { margin: 0 0 10px 0; font-size: 24px; letter-spacing: 1px; }
            .leaderboard-players { padding: 15px; background-color: #ffffff; }
            .player-row { display: flex; align-items: center; padding: 12px 15px; margin-bottom: 10px; background-color: #f8f9fa; border-radius: 8px; transition: transform 0.2s ease; }
            .player-row:hover { transform: scale(1.02); background-color: #f1f3f5; }
            .rank { font-weight: bold; width: 30px; font-size: 16px; color: #6c757d; }
            .player-row:nth-child(1) .rank { color: #ffd700; font-size: 20px; } 
            .player-row:nth-child(2) .rank { color: #c0c0c0; font-size: 18px; } 
            .player-row:nth-child(3) .rank { color: #cd7f32; font-size: 17px; } 
            .avatar { width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-right: 15px; font-size: 14px; }
            .name { flex-grow: 1; font-weight: 600; color: #333333; }
            .score { font-weight: bold; color: #764ba2; }
        </style>
        <title>OOP Leaderboard</title>
    </head>
    <body>
        <div class="container">
            <div class="leaderboard_background">
                <h2>Top Players</h2>
                <div>OOP & SQLite Architecture</div>
            </div>
            <div class="leaderboard-players">
                
                <?php if (empty($topPlayers)): ?>
                    <div style="text-align: center; padding: 20px; color: #6c757d;">No rankings available.</div>
                <?php else: ?>
                    <?php 
                    $rank = 1; 
                    foreach ($topPlayers as $player): 
                    ?>
                        <div class="player-row">
                            <div class="rank"><?= $rank++; ?></div>
                            <div class="avatar" style="background-color: <?= htmlspecialchars($player->avatarColor); ?>; color: <?= htmlspecialchars($player->textColor); ?>;">
                                <?= htmlspecialchars($player->initials); ?>
                            </div>
                            <div class="name"><?= $player->getNameEscaped(); ?></div>
                            <div class="score"><?= $player->getFormattedScore(); ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

            </div>
        </div>
    </body>
</html>
