
CREATE TABLE IF NOT EXISTS `dc_oauth_token` (
  `id` int(11) NOT NULL,
  `shopId` int(11) NOT NULL,
  `token` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `validTill` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `dc_oauth_token`
--
ALTER TABLE `dc_oauth_token`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shopId` (`shopId`),
  ADD KEY `validTill` (`validTill`);
ALTER TABLE `dc_oauth_token`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;