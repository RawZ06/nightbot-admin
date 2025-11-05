<?php
declare(strict_types=1);

namespace App\Models;

use App\Database;
use PDO;

class Command
{
    public ?int $id = null;
    public string $name;
    public ?string $description = null;
    public string $code;
    public ?string $created_at = null;
    public ?string $updated_at = null;

    public static function all(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query('SELECT * FROM commands ORDER BY name ASC');
        return array_map(fn($row) => self::fromArray($row), $stmt->fetchAll());
    }

    public static function find(int $id): ?self
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM commands WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? self::fromArray($row) : null;
    }

    public static function findByName(string $name): ?self
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM commands WHERE name = :name');
        $stmt->execute(['name' => $name]);
        $row = $stmt->fetch();

        return $row ? self::fromArray($row) : null;
    }

    public function save(): bool
    {
        $db = Database::getConnection();

        if ($this->id === null) {
            // Insert
            $stmt = $db->prepare(
                'INSERT INTO commands (name, description, code) VALUES (:name, :description, :code) RETURNING id'
            );
            $stmt->execute([
                'name' => $this->name,
                'description' => $this->description,
                'code' => $this->code,
            ]);
            $this->id = (int) $stmt->fetchColumn();
            return true;
        } else {
            // Update
            $stmt = $db->prepare(
                'UPDATE commands SET name = :name, description = :description, code = :code WHERE id = :id'
            );
            return $stmt->execute([
                'id' => $this->id,
                'name' => $this->name,
                'description' => $this->description,
                'code' => $this->code,
            ]);
        }
    }

    public function delete(): bool
    {
        if ($this->id === null) {
            return false;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare('DELETE FROM commands WHERE id = :id');
        return $stmt->execute(['id' => $this->id]);
    }

    private static function fromArray(array $data): self
    {
        $command = new self();
        $command->id = (int) $data['id'];
        $command->name = $data['name'];
        $command->description = $data['description'];
        $command->code = $data['code'];
        $command->created_at = $data['created_at'];
        $command->updated_at = $data['updated_at'];
        return $command;
    }
}
