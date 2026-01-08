<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260108104500DeleteGames extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Delete Ajedrez and Pong games';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("DELETE FROM juegos WHERE nombre IN ('Ajedrez', 'Pong')");
    }

    public function down(Schema $schema): void
    {
        // Downgrade: restore the games if needed
        $this->addSql("INSERT INTO juegos (nombre, activo, token) VALUES ('Ajedrez', 1, 'ajedrez_token'), ('Pong', 1, 'pong_token')");
    }
}
