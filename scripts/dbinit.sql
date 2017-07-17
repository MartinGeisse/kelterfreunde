
CREATE TABLE `kelterfreunde`.`buchungen` (
    `id` BIGINT NOT NULL AUTO_INCREMENT,
    `jahr` SMALLINT NOT NULL,
    `monat` TINYINT NOT NULL,
    `tag` TINYINT NOT NULL,
    `blocknummer` TINYINT NOT NULL,
    `slotnummer` TINYINT NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `telefonnummer` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;
ALTER TABLE `kelterfreunde`.`buchungen` ADD UNIQUE `zeitindex` (`jahr`, `monat`, `tag`, `blocknummer`, `slotnummer`);
