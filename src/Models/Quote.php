<?php
declare(strict_types=1);

namespace App\Models;

use App\Database;
use PDO;

class Quote
{
    public ?int $id = null;
    public string $name;
    public string $token;
    public ?string $description = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;

    public static function all(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query('SELECT * FROM quotes ORDER BY name ASC');
        return array_map(fn($row) => self::fromArray($row), $stmt->fetchAll());
    }

    public static function find(int $id): ?self
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM quotes WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? self::fromArray($row) : null;
    }

    public static function findByName(string $name): ?self
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM quotes WHERE name = :name');
        $stmt->execute(['name' => $name]);
        $row = $stmt->fetch();

        return $row ? self::fromArray($row) : null;
    }

    public static function findByToken(string $token): ?self
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM quotes WHERE token = :token');
        $stmt->execute(['token' => $token]);
        $row = $stmt->fetch();

        return $row ? self::fromArray($row) : null;
    }

    public function save(): bool
    {
        $db = Database::getConnection();

        if ($this->id === null) {
            // Generate token if not set
            if (empty($this->token)) {
                $this->token = bin2hex(random_bytes(16));
            }

            // Insert
            $stmt = $db->prepare(
                'INSERT INTO quotes (name, token, description) VALUES (:name, :token, :description) RETURNING id'
            );
            $stmt->execute([
                'name' => $this->name,
                'token' => $this->token,
                'description' => $this->description,
            ]);
            $this->id = (int) $stmt->fetchColumn();
            return true;
        } else {
            // Update
            $stmt = $db->prepare(
                'UPDATE quotes SET name = :name, description = :description WHERE id = :id'
            );
            return $stmt->execute([
                'id' => $this->id,
                'name' => $this->name,
                'description' => $this->description,
            ]);
        }
    }

    public function delete(): bool
    {
        if ($this->id === null) {
            return false;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare('DELETE FROM quotes WHERE id = :id');
        return $stmt->execute(['id' => $this->id]);
    }

    public function getItems(): array
    {
        if ($this->id === null) {
            return [];
        }

        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM quote_items WHERE quote_id = :quote_id ORDER BY id ASC');
        $stmt->execute(['quote_id' => $this->id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addItem(string $value): int
    {
        $db = Database::getConnection();
        $stmt = $db->prepare(
            'INSERT INTO quote_items (quote_id, value) VALUES (:quote_id, :value) RETURNING id'
        );
        $stmt->execute([
            'quote_id' => $this->id,
            'value' => $value,
        ]);
        return (int) $stmt->fetchColumn();
    }

    public function removeItem(int $itemId): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('DELETE FROM quote_items WHERE id = :id AND quote_id = :quote_id');
        return $stmt->execute(['id' => $itemId, 'quote_id' => $this->id]);
    }

    public function getRandomItem(): ?array
    {
        if ($this->id === null) {
            return null;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM quote_items WHERE quote_id = :quote_id ORDER BY RANDOM() LIMIT 1');
        $stmt->execute(['quote_id' => $this->id]);
        return $stmt->fetch() ?: null;
    }

    public function getItemCount(): int
    {
        if ($this->id === null) {
            return 0;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT COUNT(*) FROM quote_items WHERE quote_id = :quote_id');
        $stmt->execute(['quote_id' => $this->id]);
        return (int) $stmt->fetchColumn();
    }

    private static function fromArray(array $data): self
    {
        $quote = new self();
        $quote->id = (int) $data['id'];
        $quote->name = $data['name'];
        $quote->token = $data['token'];
        $quote->description = $data['description'];
        $quote->created_at = $data['created_at'];
        $quote->updated_at = $data['updated_at'];
        return $quote;
    }
}
