<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260108000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create initial database schema';
    }

    public function up(Schema $schema): void
    {
        // User table
        $this->addSql('CREATE TABLE "user" (id INTEGER PRIMARY KEY AUTOINCREMENT, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, token VARCHAR(255) NOT NULL, nombre VARCHAR(150), activo BOOLEAN)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email)');

        // Aplicaciones table
        $this->addSql('CREATE TABLE aplicaciones (id INTEGER PRIMARY KEY AUTOINCREMENT, nombre VARCHAR(150) NOT NULL, activo BOOLEAN, apikey VARCHAR(255))');

        // Juegos table
        $this->addSql('CREATE TABLE juegos (id INTEGER PRIMARY KEY AUTOINCREMENT, aplicacion_id INTEGER, nombre VARCHAR(150) NOT NULL, activo BOOLEAN, token VARCHAR(255) NOT NULL, FOREIGN KEY (aplicacion_id) REFERENCES aplicaciones(id) ON DELETE SET NULL)');
        $this->addSql('CREATE INDEX IDX_JUEGOS_APLICACION ON juegos(aplicacion_id)');

        // Puntuaciones table
        $this->addSql('CREATE TABLE puntuaciones (id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER, juego_id INTEGER, puntuacion INTEGER NOT NULL, fecha DATETIME NOT NULL, FOREIGN KEY (user_id) REFERENCES "user"(id) ON DELETE CASCADE, FOREIGN KEY (juego_id) REFERENCES juegos(id) ON DELETE CASCADE)');
        $this->addSql('CREATE INDEX IDX_PUNTUACIONES_USER ON puntuaciones(user_id)');
        $this->addSql('CREATE INDEX IDX_PUNTUACIONES_JUEGO ON puntuaciones(juego_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE puntuaciones');
        $this->addSql('DROP TABLE juegos');
        $this->addSql('DROP TABLE aplicaciones');
        $this->addSql('DROP TABLE "user"');
    }
}
