-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sty 29, 2025 at 05:15 PM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `portal_ogloszeniowy`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `kategorie`
--

CREATE TABLE `kategorie` (
  `kategoria_ID` int(11) NOT NULL,
  `nazwa` varchar(100) NOT NULL,
  `opis` varchar(256) DEFAULT NULL,
  `plik_sciezka` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategorie`
--

INSERT INTO `kategorie` (`kategoria_ID`, `nazwa`, `opis`, `plik_sciezka`) VALUES
(1, 'Motoryzacja', 'Ogłoszenia związane z pojazdami', 'motoryzacja.png'),
(2, 'Nieruchomości', 'Domy, mieszkania, działki', 'nieruchomosci.png'),
(3, 'Elektronika', 'Sprzęt elektroniczny i AGD', 'elektronika.png'),
(4, 'Moda', 'Odzież, obuwie, dodatki', 'moda.png'),
(5, 'Usługi', 'Różnego rodzaju usługi', 'uslugi.png'),
(6, 'Edukacja', 'Szkolenia, korepetycje', 'edukacja.png'),
(7, 'Sport', 'Sprzęt i akcesoria sportowe', 'sport.png'),
(8, 'Zdrowie', 'Produkty i usługi zdrowotne', 'zdrowie.png'),
(9, 'Hobby', 'Rękodzieło, gry, kolekcje', 'hobby.png'),
(10, 'Zwierzęta', 'Akcesoria i zwierzęta', 'zwierzeta.png');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `ogloszenia`
--

CREATE TABLE `ogloszenia` (
  `ogloszenie_ID` int(16) NOT NULL,
  `tytul` varchar(40) NOT NULL,
  `cena` float NOT NULL,
  `kategoria_ID` int(16) NOT NULL,
  `opis` varchar(256) NOT NULL,
  `miasto` varchar(256) NOT NULL,
  `data_zalozenia` date NOT NULL,
  `uzytkownik_ID` int(16) NOT NULL,
  `status` enum('nieaktywne','aktywne','oczekujace','anulowane przez administratora') DEFAULT 'oczekujace'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ogloszenia`
--

INSERT INTO `ogloszenia` (`ogloszenie_ID`, `tytul`, `cena`, `kategoria_ID`, `opis`, `miasto`, `data_zalozenia`, `uzytkownik_ID`, `status`) VALUES
(1, 'Sprzedam samochód', 25000, 1, 'Auto w dobrym stanie', '', '2024-12-01', 1, 'aktywne'),
(2, 'Wynajem mieszkania', 1500, 2, 'Mieszkanie dwupokojowe', '', '2024-12-02', 2, 'aktywne'),
(3, 'Laptop na sprzedaż', 2000, 3, 'Laptop używany, dobry stan', 'Łódz', '2024-12-03', 3, 'aktywne'),
(4, 'Kurtka zimowa', 150, 4, 'Nowa kurtka, rozmiar M', 'Radom', '2024-12-04', 4, 'aktywne'),
(5, 'Usługi remontowe', 5000, 5, 'Kompleksowe remonty mieszkań', '', '2024-12-05', 5, 'anulowane przez administratora'),
(6, 'Korepetycje z matematyki', 50, 6, 'Korepetycje online', '', '2024-12-06', 6, 'oczekujace'),
(7, 'Sprzęt sportowy', 300, 7, 'Hantle, ciężarki', '', '2024-12-07', 7, 'aktywne'),
(8, 'Zioła lecznicze', 100, 8, 'Naturalne zioła', '', '2024-12-08', 8, 'anulowane przez administratora'),
(10, 'Pies do adopcji', 0, 10, 'Szukamy domu dla psa', '', '2024-12-10', 10, 'oczekujace'),
(21, 'basen', 300000, 2, 'basen', 'Gliwice', '2025-01-18', 11, 'anulowane przez administratora'),
(22, 'Karambit', 10000, 3, 'KOszaa', 'Szczecin', '2025-01-18', 11, 'oczekujace'),
(23, 'karambit', 22000, 3, 'kosza', 'Gdańsk', '2025-01-18', 11, 'aktywne'),
(24, 'Koło', 23, 1, 'Kołol', 'Szczecin', '2025-01-22', 16, 'aktywne'),
(25, 'Rower składak', 250, 1, 'Nowy nie używany', 'Radom', '2025-01-29', 20, 'aktywne'),
(26, 'Skuter perła ', 400, 1, 'Lekko używany ', 'Sosnowiec', '2025-01-29', 21, 'aktywne');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `ogloszenie_zdjecia`
--

CREATE TABLE `ogloszenie_zdjecia` (
  `zdjecie_ID` int(16) NOT NULL,
  `ogloszenie_ID` int(16) NOT NULL,
  `plik_sciezka` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ogloszenie_zdjecia`
--

INSERT INTO `ogloszenie_zdjecia` (`zdjecie_ID`, `ogloszenie_ID`, `plik_sciezka`) VALUES
(7, 21, 'img/21/basen2.jpg'),
(8, 21, 'img/21/Designer (1).jpg'),
(9, 21, 'img/21/Designer.jpg'),
(11, 23, 'img/23/weapon_knife_karambit_am_marked_up_fine_light_png.png'),
(12, 23, 'img/23/karambit-fade-1A.jpg'),
(15, 24, 'img/24/kolo-kompletne-star-ciagnik-sam-mucharzew-574945158.jpg'),
(16, 22, 'img/22/a.jpg'),
(17, 25, 'img/25/pobrane.jpg'),
(18, 26, 'img/26/images.jpg');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `uzytkownicy`
--

CREATE TABLE `uzytkownicy` (
  `uzytkownik_ID` int(16) NOT NULL,
  `nazwa` varchar(256) NOT NULL,
  `imie` varchar(100) NOT NULL,
  `nazwisko` varchar(100) NOT NULL,
  `email` varchar(256) NOT NULL,
  `haslo` varchar(256) NOT NULL,
  `numer` int(16) NOT NULL,
  `data_dolaczenia` date DEFAULT NULL,
  `rodzaj_konta` enum('uzytkownik','admin') NOT NULL DEFAULT 'uzytkownik'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `uzytkownicy`
--

INSERT INTO `uzytkownicy` (`uzytkownik_ID`, `nazwa`, `imie`, `nazwisko`, `email`, `haslo`, `numer`, `data_dolaczenia`, `rodzaj_konta`) VALUES
(1, 'AnnaKowalska', '', '', 'anna.kowalska@gmail.com', 'haslo123', 501234567, '2022-09-20', 'uzytkownik'),
(2, 'JanNowak', '', '', 'jan.nowak@gmail.com', 'bezpiecznehaslo', 502345678, '2020-06-17', 'uzytkownik'),
(3, 'KatarzynaZielinska', '', '', 'k.zielinska@gmail.com', 'katarzyna123', 503456789, '2018-01-16', 'uzytkownik'),
(4, 'PiotrWiśniewski', '', '', 'p.wisniewski@gmail.com', 'piotrek456', 504567890, '2021-04-15', 'uzytkownik'),
(5, 'MagdalenaMazur', '', '', 'm.mazur@gmail.com', 'magda789', 505678901, '2020-07-21', 'uzytkownik'),
(6, 'TomaszKowalczyk', '', '', 't.kowalczyk@gmail.com', 'tomasz123', 506789012, '2021-09-22', 'uzytkownik'),
(7, 'BarbaraLewandowska', '', '', 'b.lewandowska@gmail.com', 'barbara456', 507890123, '2019-10-08', 'uzytkownik'),
(8, 'MichałWójcik', '', '', 'm.wojcik@gmail.com', 'michal789', 508901234, '2015-10-20', 'uzytkownik'),
(10, 'PawełKamiński', '', '', 'p.kaminski@gmail.com', 'pawel456', 501123456, '2017-05-10', 'uzytkownik'),
(11, 'test1', 'Test', 'Test', 'testowe@wp.pl', '$2y$10$1szFVKi.d6P0W5ai/a3Mm.cHUWAFXtKWUhMSyO3wsA9sCy5PDegA6', 123, '2024-01-10', 'admin'),
(14, 'testowe3', 'Test', 'Test', 'test1@wp.pl', '$2y$10$3q2fFO/6gXlL78WZP4EW7.mqre7Fups15hppI2b6AJAfPe8hnh.F6', 123543654, '2016-01-12', 'uzytkownik'),
(15, 'testowe23', 'Test', 'Test', 'testowe32@wp.pl', '$2y$10$o7pcf.7.M7hIcljvF8yUuuSuNO7wMST3kFGX8owgaQm4c4eZC3n1C', 143234543, '2022-01-18', 'uzytkownik'),
(16, 'Janklan', 'Jan', 'Klan', 'jan.klan@wp.pl', '$2y$10$geKzYn8jicPruNgKwJ69B.BG2lozXxmeV88ov9PhFYeG0TDtOqQ8K', 324232432, '2025-01-09', 'uzytkownik'),
(17, 'KamilKOC', 'Kamil', 'Koc', 'kami.koc@wp.pl', '$2y$10$UUvO6qxvft/i7RjC..DqyuLXgxwCv93itQppbFAWqnzyEl0XyfLEa', 23243232, '2025-01-22', 'uzytkownik'),
(18, 'Koza', 'ALa', 'Koza', 'ala.koza@wp.pl', '$2y$10$s.fmwUE78DuXl1omKqq5U.CzEsmUkglksZQyD.Ljd5dhIsH.Cz.SO', 432343232, '2025-01-22', 'uzytkownik'),
(19, 'Ola32', 'Jan', 'Osa', 'jan.osa@wp.pl', '$2y$10$LRqx/hFwPZU4VGQ7BIPBu.JBXS2ijqh9d0.69GU4Z.DSiGEsZ5uKa', 322123213, '2025-01-22', 'uzytkownik'),
(20, 'JanPan', 'Jan ', 'Pan', 'jan.pan@wp.pl', '$2y$10$UK0EKdpGPfBIgqkpd5RmNuTtPKt6lqqLv6DfYjEtoT6m8CrvvAVqe', 433221312, '2025-01-25', 'uzytkownik'),
(21, 'Ola', 'Wola', 'wola', 'ola.wola@wp.pl', '$2y$10$2qTUE/vTQonpDCMttnPQS.hIKbL0ulNALYnJYETWjucsNj0eOo.Wq', 342324323, '2025-01-29', 'uzytkownik'),
(22, 'Admin', 'admin', 'admin', 'admin@admin.pl', '$2y$10$btLyleKIfU9xeYtp0VtIFOZZnYv4B/ax00rdjR7lPnnuSeXRwliTK', 999999999, '2025-01-29', 'admin');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `wiadomosci`
--

CREATE TABLE `wiadomosci` (
  `wiadomosc_ID` int(11) NOT NULL,
  `nadawca_ID` int(11) NOT NULL,
  `odbiorca_ID` int(11) NOT NULL,
  `tresc` varchar(1000) NOT NULL,
  `data_wyslania` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wiadomosci`
--

INSERT INTO `wiadomosci` (`wiadomosc_ID`, `nadawca_ID`, `odbiorca_ID`, `tresc`, `data_wyslania`) VALUES
(1, 1, 2, 'Witam, jestem zainteresowany ogłoszeniem.', '2024-12-19 10:04:42'),
(2, 2, 1, 'Dziękuję za wiadomość, kiedy możemy porozmawiać?', '2024-12-19 10:04:42'),
(3, 3, 4, 'Czy oferta jest nadal aktualna?', '2024-12-19 10:04:42'),
(4, 4, 3, 'Tak, zapraszam do kontaktu telefonicznego.', '2024-12-19 10:04:42'),
(5, 5, 6, 'Chciałbym dowiedzieć się więcej na temat oferty.', '2024-12-19 10:04:42'),
(6, 6, 5, 'Wszystkie szczegóły są w ogłoszeniu.', '2024-12-19 10:04:42'),
(7, 7, 8, 'Czy można negocjować cenę?', '2024-12-19 10:04:42'),
(8, 8, 7, 'Cena jest do lekkiej negocjacji.', '2024-12-19 10:04:42'),
(9, 11, 10, 'Jestem zainteresowany adopcją psa.', '2024-12-19 10:04:42'),
(10, 10, 11, 'Proszę o kontakt telefoniczny, ustalimy szczegóły.', '2024-12-19 10:04:42'),
(11, 11, 20, 'woda', '2025-01-25 19:18:46'),
(12, 11, 20, 'pies\n', '2025-01-25 20:03:45'),
(13, 11, 20, 'jak', '2025-01-25 20:04:25'),
(14, 20, 11, 'jhj', '2025-01-25 20:04:33'),
(15, 20, 11, 'qwdasdasda', '2025-01-25 20:19:01'),
(16, 20, 20, 'jan', '2025-01-25 20:22:59'),
(17, 20, 20, 'pomoc\n', '2025-01-25 20:26:39'),
(18, 20, 20, 'fdssdf', '2025-01-25 20:29:43'),
(19, 20, 11, 'co jest\n', '2025-01-25 20:31:36'),
(20, 20, 20, 'pies', '2025-01-25 20:33:07'),
(21, 11, 14, 'jan\n', '2025-01-25 20:34:11'),
(22, 11, 20, 'jan\n', '2025-01-25 20:34:11'),
(23, 20, 11, 'koza\n', '2025-01-25 20:39:05'),
(24, 20, 11, 'elo\n', '2025-01-25 20:48:54'),
(25, 20, 11, 'osddsd', '2025-01-25 20:49:08'),
(26, 11, 14, 'ddas', '2025-01-25 20:51:15'),
(27, 11, 20, 'ddas', '2025-01-25 20:51:15'),
(28, 20, 14, 'fdsfsdfs', '2025-01-25 20:52:35'),
(29, 20, 20, 'fdsfsdfs', '2025-01-25 20:52:35'),
(30, 20, 14, 'fsddsf\n', '2025-01-25 20:54:05'),
(31, 20, 20, 'fsddsf\n', '2025-01-25 20:54:05'),
(32, 20, 11, 'Aktualne?', '2025-01-25 21:19:59'),
(33, 20, 11, 'Aktualne?', '2025-01-25 21:19:59'),
(34, 20, 16, 'cxccxvvxcv', '2025-01-25 21:20:15'),
(35, 20, 14, 'fdsfsd', '2025-01-25 21:28:10'),
(36, 11, 20, 'vxcvcxv', '2025-01-25 21:28:39'),
(37, 11, 11, 'cwccsdsadsfdsfds', '2025-01-25 21:39:50'),
(38, 11, 11, 'fd', '2025-01-25 21:41:49'),
(39, 11, 11, 'fdd', '2025-01-29 12:17:33'),
(40, 11, 11, 'gfdgdf', '2025-01-29 12:18:01'),
(41, 11, 11, 'aktualne?', '2025-01-29 12:19:10'),
(42, 11, 11, 'dfgdfgdfgdf', '2025-01-29 12:19:24'),
(43, 11, 11, 'akt?', '2025-01-29 12:19:42'),
(44, 11, 11, 'fdf', '2025-01-29 12:19:49'),
(45, 11, 20, 'fsdfsdfsdf', '2025-01-29 12:22:31'),
(46, 11, 20, 'dasasdas', '2025-01-29 12:25:01'),
(47, 20, 11, 'woda\n', '2025-01-29 12:29:33'),
(48, 20, 11, 'aktujalne?', '2025-01-29 12:30:10'),
(49, 11, 20, 'Podaj rozmiar ramy>?', '2025-01-29 12:32:17'),
(50, 11, 21, 'Aktualne?', '2025-01-29 12:34:51'),
(51, 11, 20, 'nowe', '2025-01-29 12:38:01'),
(52, 11, 20, 'sdfsdf', '2025-01-29 12:39:11'),
(53, 11, 20, 'ds', '2025-01-29 12:39:35'),
(54, 11, 20, 'GFDGDF', '2025-01-29 12:41:46'),
(55, 11, 21, 'siema ', '2025-01-29 14:58:54'),
(56, 11, 1, '20 zl dam', '2025-01-29 14:59:54'),
(57, 11, 14, 'dasdsa', '2025-01-29 15:02:34'),
(58, 21, 11, 'sdsds\n', '2025-01-29 16:02:17'),
(59, 21, 14, 'sdsds\n', '2025-01-29 16:02:17'),
(60, 21, 11, 'fds', '2025-01-29 16:02:19'),
(61, 21, 14, 'fds', '2025-01-29 16:02:19'),
(62, 21, 11, 'fsdf', '2025-01-29 16:02:21'),
(63, 21, 14, 'fsdf', '2025-01-29 16:02:21'),
(64, 21, 11, 's', '2025-01-29 16:04:06'),
(65, 21, 14, 's', '2025-01-29 16:04:06'),
(66, 21, 22, 'sdsd', '2025-01-29 16:07:39'),
(67, 21, 22, 'dss', '2025-01-29 16:14:01'),
(68, 21, 22, 'sdsd', '2025-01-29 16:19:19'),
(69, 21, 22, 'fsdfsdfsdf', '2025-01-29 16:19:39'),
(70, 22, 11, 'dsad', '2025-01-29 16:24:50'),
(71, 22, 11, 'sdfsdf', '2025-01-29 16:27:27'),
(72, 21, 22, 'dss', '2025-01-29 16:30:03'),
(73, 22, 11, 'dasdas', '2025-01-29 16:30:06'),
(76, 22, 21, 'dsds', '2025-01-29 16:35:45'),
(77, 22, 21, 'elo\n', '2025-01-29 16:35:51'),
(78, 22, 21, 'witam\n', '2025-01-29 16:36:02'),
(79, 21, 22, 'adsaddas', '2025-01-29 16:36:42'),
(80, 22, 21, 'adasd', '2025-01-29 16:38:45'),
(81, 21, 22, 'woda\n', '2025-01-29 16:38:52'),
(82, 21, 22, 'wds', '2025-01-29 16:40:38'),
(83, 22, 21, 'fdf', '2025-01-29 16:40:41'),
(84, 20, 22, 'dasdasd', '2025-01-29 16:45:37'),
(85, 20, 22, 'csdsd', '2025-01-29 16:50:56'),
(86, 20, 22, 'cascascascs', '2025-01-29 16:52:22'),
(87, 22, 20, 'asdasdsadasd', '2025-01-29 16:52:26'),
(88, 11, 21, 'fdsfdsfsdf', '2025-01-29 17:00:28');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `zgloszenia`
--

CREATE TABLE `zgloszenia` (
  `zgloszenie_ID` int(16) NOT NULL,
  `rodzaj` enum('Spam','Nieprawidłowe dane sprzedającego','Oszustwo','Szkodliwe Tresci','Inne') NOT NULL,
  `uzytkownik_ID` int(16) NOT NULL,
  `ogloszenie_ID` int(16) NOT NULL,
  `opis` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `zgloszenia`
--

INSERT INTO `zgloszenia` (`zgloszenie_ID`, `rodzaj`, `uzytkownik_ID`, `ogloszenie_ID`, `opis`) VALUES
(1, 'Spam', 1, 1, 'Ogłoszenie wydaje się być spamem'),
(2, '', 2, 2, 'Zawiera obraźliwe treści'),
(3, '', 3, 3, 'Ogłoszenie powinno być w innej kategorii'),
(4, 'Oszustwo', 4, 4, 'Podejrzane ogłoszenie'),
(5, '', 5, 5, 'Brak istotnych informacji'),
(6, 'Spam', 6, 6, 'Powtarzające się treści'),
(7, '', 7, 7, 'Zawiera obraźliwe treści'),
(11, 'Spam', 11, 24, 'wielokratnen dodawanie tego samego'),
(12, 'Oszustwo', 11, 26, 'to je moje zlodziej'),
(13, 'Oszustwo', 11, 26, 'ókradł'),
(14, 'Spam', 11, 26, 'dfgdfg');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `kategorie`
--
ALTER TABLE `kategorie`
  ADD PRIMARY KEY (`kategoria_ID`);

--
-- Indeksy dla tabeli `ogloszenia`
--
ALTER TABLE `ogloszenia`
  ADD PRIMARY KEY (`ogloszenie_ID`),
  ADD KEY `fk_ogloszenia_uzytkownicy` (`uzytkownik_ID`),
  ADD KEY `FK_kategoria` (`kategoria_ID`);

--
-- Indeksy dla tabeli `ogloszenie_zdjecia`
--
ALTER TABLE `ogloszenie_zdjecia`
  ADD PRIMARY KEY (`zdjecie_ID`),
  ADD KEY `fk_ogloszenie_zdjecia_ogloszenia` (`ogloszenie_ID`);

--
-- Indeksy dla tabeli `uzytkownicy`
--
ALTER TABLE `uzytkownicy`
  ADD PRIMARY KEY (`uzytkownik_ID`),
  ADD UNIQUE KEY `UNIQUE_nazwa` (`nazwa`);

--
-- Indeksy dla tabeli `wiadomosci`
--
ALTER TABLE `wiadomosci`
  ADD PRIMARY KEY (`wiadomosc_ID`),
  ADD KEY `nadawca_ID` (`nadawca_ID`),
  ADD KEY `odbiorca_ID` (`odbiorca_ID`);

--
-- Indeksy dla tabeli `zgloszenia`
--
ALTER TABLE `zgloszenia`
  ADD PRIMARY KEY (`zgloszenie_ID`),
  ADD KEY `fk_zgloszenia_uzytkownicy` (`uzytkownik_ID`),
  ADD KEY `fk_zgloszenia_ogloszenia` (`ogloszenie_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kategorie`
--
ALTER TABLE `kategorie`
  MODIFY `kategoria_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `ogloszenia`
--
ALTER TABLE `ogloszenia`
  MODIFY `ogloszenie_ID` int(16) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `ogloszenie_zdjecia`
--
ALTER TABLE `ogloszenie_zdjecia`
  MODIFY `zdjecie_ID` int(16) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `uzytkownicy`
--
ALTER TABLE `uzytkownicy`
  MODIFY `uzytkownik_ID` int(16) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `wiadomosci`
--
ALTER TABLE `wiadomosci`
  MODIFY `wiadomosc_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `zgloszenia`
--
ALTER TABLE `zgloszenia`
  MODIFY `zgloszenie_ID` int(16) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ogloszenia`
--
ALTER TABLE `ogloszenia`
  ADD CONSTRAINT `FK_kategoria` FOREIGN KEY (`kategoria_ID`) REFERENCES `kategorie` (`kategoria_ID`),
  ADD CONSTRAINT `fk_ogloszenia_uzytkownicy` FOREIGN KEY (`uzytkownik_ID`) REFERENCES `uzytkownicy` (`uzytkownik_ID`);

--
-- Constraints for table `ogloszenie_zdjecia`
--
ALTER TABLE `ogloszenie_zdjecia`
  ADD CONSTRAINT `fk_ogloszenie_zdjecia_ogloszenia` FOREIGN KEY (`ogloszenie_ID`) REFERENCES `ogloszenia` (`ogloszenie_ID`) ON DELETE CASCADE;

--
-- Constraints for table `wiadomosci`
--
ALTER TABLE `wiadomosci`
  ADD CONSTRAINT `fk_wiadomosci_nadawca` FOREIGN KEY (`nadawca_ID`) REFERENCES `uzytkownicy` (`uzytkownik_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_wiadomosci_odbiorca` FOREIGN KEY (`odbiorca_ID`) REFERENCES `uzytkownicy` (`uzytkownik_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `wiadomosci_ibfk_1` FOREIGN KEY (`nadawca_ID`) REFERENCES `uzytkownicy` (`uzytkownik_ID`),
  ADD CONSTRAINT `wiadomosci_ibfk_2` FOREIGN KEY (`odbiorca_ID`) REFERENCES `uzytkownicy` (`uzytkownik_ID`);

--
-- Constraints for table `zgloszenia`
--
ALTER TABLE `zgloszenia`
  ADD CONSTRAINT `fk_zgloszenia_ogloszenia` FOREIGN KEY (`ogloszenie_ID`) REFERENCES `ogloszenia` (`ogloszenie_ID`),
  ADD CONSTRAINT `fk_zgloszenia_uzytkownicy` FOREIGN KEY (`uzytkownik_ID`) REFERENCES `uzytkownicy` (`uzytkownik_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
