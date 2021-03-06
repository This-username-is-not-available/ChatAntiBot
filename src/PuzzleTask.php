<?php
declare(strict_types=1);

namespace App;

use App\Dto\PuzzleTaskDto;
use App\Dto\PuzzleTaskUserDto;
use SQLite3;

class PuzzleTask
{
    /**
     * @var SQLite3
     */
    private $database;

    public function __construct(SQLite3 $database)
    {
        $this->database = $database;
    }

    /**
     * @param int $chatId
     * @param int $userId
     * @return PuzzleTaskDto|null
     */
    public function getPuzzleTask(int $chatId, int $userId): ?PuzzleTaskDto
    {
        $results = $this->database->query(
            sprintf(
                'SELECT answer, message_id FROM puzzle_task WHERE chat_id = %d AND user_id = %d',
                $chatId,
                $userId
            )
        );
        if (false === $results) {
            return null;
        }
        $row = $results->fetchArray();
        if (false === $row) {
            return null;
        }
        if (empty($row)) {
            return null;
        }

        return new PuzzleTaskDto($chatId, $userId, (string)$row['answer'], $row['message_id']);
    }

    /**
     * @param int $chatId
     * @param int $userId
     * @param string $answer
     * @param int $messageId
     * @return void
     */
    public function savePuzzleTask(int $chatId, int $userId, string $answer, int $messageId): void
    {
        $this->database->query(
            sprintf(
                "INSERT OR REPLACE INTO puzzle_task
    (chat_id, user_id, answer, message_id, date_time)
     VALUES (%d, %d, '%s', %d, CURRENT_TIMESTAMP)",
                $chatId,
                $userId,
                $answer,
                $messageId
            )
        );
    }

    /**
     * @param int $chatId
     * @param int $userId
     */
    public function deletePuzzleTask(int $chatId, int $userId): void
    {
        $this->database->query(
            sprintf("DELETE FROM puzzle_task WHERE chat_id = %d AND user_id = %d", $chatId, $userId)
        );
    }

    /**
     * @param int $timeOutMinutes
     * @return PuzzleTaskUserDto[]
     */
    public function getNonApprovedUsers(int $timeOutMinutes): array
    {
        $users = [];
        $query = sprintf(
            "SELECT chat_id, user_id FROM puzzle_task WHERE `date_time` < datetime('now', '-%d minute')",
            $timeOutMinutes
        );
        $results = $this->database->query(
            $query
        );
        while ($row = $results->fetchArray()) {
            $users[] = new PuzzleTaskUserDto($row['chat_id'], $row['user_id']);
        }

        return $users;
    }
}
