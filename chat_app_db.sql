-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 06, 2026 at 03:42 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `chat_app_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `creator_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `name`, `admin_id`, `creator_id`) VALUES
(2, 'Tech World🤖', NULL, NULL),
(11, 'Vibe Nation💃🗣️', NULL, NULL),
(13, 'Digital Horizons🖥️🖥️', NULL, NULL),
(15, 'The Launch Pad🥸💻', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `group_users`
--

CREATE TABLE `group_users` (
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` enum('user','admin','','') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `group_users`
--

INSERT INTO `group_users` (`group_id`, `user_id`, `role`) VALUES
(2, 1, 'admin'),
(2, 2, 'user'),
(2, 6, 'user'),
(2, 7, 'user'),
(2, 10, 'user'),
(2, 17, 'user'),
(2, 19, 'user'),
(2, 25, 'user'),
(11, 1, 'user'),
(11, 2, 'admin'),
(11, 6, 'user'),
(11, 7, 'user'),
(11, 10, 'user'),
(13, 1, 'admin'),
(13, 2, 'user'),
(15, 1, 'admin'),
(15, 2, 'user'),
(15, 6, 'user'),
(15, 7, 'user'),
(15, 10, 'user'),
(29, 2, 'user'),
(44, 2, 'user'),
(67, 2, 'user');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `group_id`, `user_id`, `content`, `file_path`, `created_at`, `timestamp`) VALUES
(108, 13, 1, 'Hello I need help', NULL, '2024-09-25 13:40:57', '2024-09-25 13:40:57'),
(110, 13, 1, 'How can i add an image in my project directory to act as a default profile picture for any group or contact', NULL, '2024-09-25 13:43:38', '2024-09-25 13:43:38'),
(114, 13, 1, 'Thanks', NULL, '2024-09-25 13:53:08', '2024-09-25 13:53:08'),
(130, 2, 1, 'Been there, done that. The CEO called me up, saying, “I think your code is sending emails to everyone in our database.”😅😅', NULL, '2024-09-27 15:39:25', '2024-09-27 15:39:25'),
(132, 2, 6, 'That’s what you get for forgetting to change the “To:” field from “*” to a specific address.', NULL, '2024-09-28 13:49:16', '2024-09-28 13:49:16'),
(133, 2, 10, 'Or using “console.log” instead of “alert()”. I had a user thinking their app was possessed because their screen was filled with debug messages.😅😅', NULL, '2024-09-28 13:50:24', '2024-09-28 13:50:24'),
(134, 2, 7, 'I still remember the time I spent hours figuring out why my CSS wasn’t working, only to realize I was editing the wrong stylesheet.', NULL, '2024-09-28 13:51:37', '2024-09-28 13:51:37'),
(143, 15, 1, 'Oh, for sure! Generative AI has taken off, especially with tools like ChatGPT and DALL-E. Businesses are using them for content creation, marketing, and even product design. It’s a game changer.', NULL, '2024-10-01 12:36:11', '2024-10-01 12:36:11'),
(144, 15, 6, 'Absolutely! I’ve been researching the advancements in natural language processing and image generation. The ability of AI to create human-like text and art is impressive. But I also worry about how easily misinformation can spread through these tools.', NULL, '2024-10-01 12:37:17', '2024-10-01 12:37:17'),
(145, 15, 7, 'That’s a critical point, Chloe. As these technologies become more powerful, we need to consider the ethical implications. How do we ensure accountability when AI-generated content is misleading or harmful?', NULL, '2024-10-01 12:38:12', '2024-10-01 12:38:12'),
(146, 15, 15, 'Right! I’m currently working on projects that emphasize fairness and transparency in AI. We need to tackle issues like bias in algorithms head-on. If the training data is flawed, the outcomes will be too.', NULL, '2024-10-01 12:38:55', '2024-10-01 12:38:55'),
(147, 15, 10, 'It’s fascinating to hear all this, but it can feel overwhelming at times. With so many trends, how do we separate the hype from what’s genuinely impactful?', NULL, '2024-10-01 12:39:52', '2024-10-01 12:39:52'),
(162, 2, 1, 'Classic. Bugs are like that—sneaky little gremlins hiding in the smallest places.', NULL, '2024-10-04 17:00:45', '2024-10-04 17:00:45'),
(163, 2, 6, 'I once spent an entire day debugging a problem only to realize I was running an outdated version of the code. 😅😅', NULL, '2024-10-04 17:01:47', '2024-10-04 17:01:47'),
(164, 2, 10, 'Sounds like my last project. I spent hours trying to fix an issue only to discover I was editing the wrong file. 😂😂😂😂', NULL, '2024-10-04 17:02:38', '2024-10-04 17:02:38'),
(165, 2, 7, 'Or the time I spent all night trying to fix a script, only to find out I was working on the “.test” file instead of the “.prod” file.', NULL, '2024-10-04 17:03:29', '2024-10-04 17:03:29'),
(167, 2, 1, 'Been there, done that. The CEO called me up, saying, “I think your code is sending emails to everyone in our database.”😅😅', NULL, '2024-10-04 17:05:14', '2024-10-04 17:05:14'),
(182, 11, 2, 'So, I tried to cook a new recipe last night. It was supposed to be a fancy risotto, but it ended up looking like… a science experiment gone wrong.', NULL, '2024-10-13 15:07:56', '2024-10-13 15:07:56'),
(183, 11, 1, 'Oh no, what happened? Did it explode or something?', NULL, '2024-10-13 15:08:57', '2024-10-13 15:08:57'),
(184, 11, 6, 'I bet Admin accidentally invented a new dish: “The Blob Special.”😂😂 ', NULL, '2024-10-14 12:44:32', '2024-10-14 12:44:32'),
(185, 11, 10, 'It sounds like a great candidate for a food blog. You could call it “Cooking Catastrophes: How Not to Use a Stove.”😂', NULL, '2024-10-14 12:45:31', '2024-10-14 12:45:31'),
(186, 11, 7, 'Or just “How to Make Your Smoke Detector a Kitchen Companion.”', NULL, '2024-10-14 12:47:31', '2024-10-14 12:47:31'),
(187, 11, 2, 'Ha ha, very funny. Actually, I think I might’ve left out a crucial step. Like the step where you actually add the ingredients.🥸', NULL, '2024-10-14 12:48:28', '2024-10-14 12:48:28'),
(188, 11, 1, 'You forgot the ingredients? I once forgot to turn on the oven. My cookies were just… well, dough.😁', NULL, '2024-10-14 12:49:49', '2024-10-14 12:49:49'),
(189, 11, 6, 'Dough cookies? That’s a new level of lazy baking.', NULL, '2024-10-14 12:50:38', '2024-10-14 12:50:38'),
(190, 11, 10, 'At least you didn’t mistake salt for sugar. I tried to bake a cake with a cup of salt. My guests thought it was a prank.😂😂😂', NULL, '2024-10-14 12:53:00', '2024-10-14 12:53:00'),
(191, 11, 7, 'It’s a cake that’ll make you cry, literally. “Salted Caramel? More like Salted Regret.”', NULL, '2024-10-14 12:54:07', '2024-10-14 12:54:07'),
(192, 11, 2, 'At least I didn’t set off the fire alarm. Last time I cooked, I tried to make toast and ended up with a charred kitchen.', NULL, '2024-10-14 12:55:16', '2024-10-14 12:55:16'),
(193, 11, 1, 'Charred toast sounds like a breakfast from “Survivor: The Kitchen Edition.”', NULL, '2024-10-14 12:55:58', '2024-10-14 12:55:58'),
(194, 11, 6, 'And I bet you’re still trying to convince people it was gourmet. “Slightly burnt, with a hint of disaster.”😅😅', NULL, '2024-10-14 12:57:00', '2024-10-14 12:57:00'),
(195, 11, 10, 'You know, we should have a cooking challenge. Each of us makes something and we all taste test. The winner gets bragging rights and the loser has to clean up.', NULL, '2024-10-14 12:58:07', '2024-10-14 12:58:07'),
(196, 11, 7, 'And the loser also has to post a photo of their creation on social media with the caption: “Gourmet masterpiece or kitchen horror?”', NULL, '2024-10-14 12:59:03', '2024-10-14 12:59:03'),
(197, 11, 2, 'Challenge accepted. But if I win, I want the prize to be a fire extinguisher😅.', NULL, '2024-10-14 13:01:46', '2024-10-14 13:01:46'),
(199, 11, 1, 'Deal. And remember, if your dish can survive a fire alarm, you’re the real chef.😁😁', NULL, '2024-10-14 13:04:22', '2024-10-14 13:04:22'),
(200, 11, 6, 'Or if it tastes good, we’ll just call it “smoky flavor.” 😅😅', NULL, '2024-10-14 13:05:16', '2024-10-14 13:05:16'),
(201, 11, 10, 'Perfect! Now, let’s see if any of us can actually make something edible. 🍔🍔', NULL, '2024-10-14 13:06:23', '2024-10-14 13:06:23'),
(202, 11, 7, 'I’m in! Here’s to cooking disasters and kitchen triumphs!', NULL, '2024-10-14 13:07:12', '2024-10-14 13:07:12'),
(271, 2, 1, 'Hey, can someone provide a code to delete a user account?', NULL, '2024-10-16 13:54:55', '2024-10-16 13:54:55'),
(290, 2, 1, 'Thanks for the help👍👍', NULL, '2024-10-16 21:15:43', '2024-10-16 21:15:43'),
(388, 2, 25, 'DCI REPRESENTATIVE🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥🔥Mhame', NULL, '2024-12-09 13:53:03', '2024-12-09 13:53:03'),
(501, 2, 2, 'So, I finally tackled that bug in my code that’s been haunting me for days. Turns out, it was just a missing semicolon😂.', NULL, '2024-12-23 19:57:18', '2024-12-23 19:57:18'),
(502, 2, 1, 'Classic. Bugs are like that—sneaky little gremlins hiding in the smallest places.', NULL, '2024-12-23 19:58:05', '2024-12-23 19:58:05'),
(503, 2, 6, 'I once spent an entire day debugging a problem only to realize I was running an outdated version of the code. ', NULL, '2024-12-23 19:58:55', '2024-12-23 19:58:55'),
(504, 2, 10, 'Sounds like my last project. I spent hours trying to fix an issue only to discover I was editing the wrong file.', NULL, '2024-12-23 19:59:31', '2024-12-23 19:59:31'),
(505, 2, 7, 'Or the time I spent all night trying to fix a script, only to find out I was working on the “.test” file instead of the “.prod” file.', NULL, '2024-12-23 20:00:07', '2024-12-23 20:00:07'),
(506, 2, 2, 'Oh man, that’s rough. At least you didn’t accidentally push a debug version to production.', NULL, '2024-12-23 20:00:33', '2024-12-23 20:00:33'),
(597, 11, 2, 'Hello there, Iam using whatsapp', NULL, '2025-03-13 16:56:49', '2025-03-13 16:56:49'),
(606, 15, 7, '🚀 Just updated my dev environment to Node.js 20 — performance boost is real! Anyone else tried it yet? 💻⚡', NULL, '2025-04-19 12:56:25', '2025-04-19 12:56:25'),
(607, 15, 7, '📱 iOS 18 rumors are getting wild… native AI features baked right in? 🤖🍎 Can’t wait for WWDC! 👀', NULL, '2025-04-19 12:57:11', '2025-04-19 12:57:11'),
(608, 15, 7, '👨💻 Working on a side project with Next.js & MongoDB — if anyone’s done token-based auth recently, I’d love to compare notes 🔐🧠', NULL, '2025-04-19 12:57:44', '2025-04-19 12:57:44'),
(611, 15, 7, '🧪 Git tip of the day: git stash push -m \"fixing bug\" — name your stashes! Makes life way easier later 🤯✨', NULL, '2025-04-19 12:59:05', '2025-04-19 12:59:05'),
(612, 15, 7, '🔥 FYI: A big Chromium update just dropped — better memory management & privacy tweaks 🧠🛡 Update your browsers, folks!', NULL, '2025-04-19 12:59:16', '2025-04-19 12:59:16'),
(613, 15, 7, '☁ Who’s playing with the new AWS Bedrock tools? Building with GenAI just got 10x easier 🚧🧠💬', NULL, '2025-04-19 13:00:08', '2025-04-19 13:00:08'),
(614, 15, 7, '😅 Just spent 2 hours debugging… turns out it was a missing semicolon. Classic dev moment 🤦♂🧵', NULL, '2025-04-19 13:00:27', '2025-04-19 13:00:27'),
(615, 15, 7, '🖥 Running into Docker container issues? Check your volume bindings — saved me from a lot of frustration earlier today 🐳💡', NULL, '2025-04-19 13:00:42', '2025-04-19 13:00:42'),
(616, 15, 7, '💡 Cool find: https://roadmap.sh/ — amazing resource for frontend/backend devs. Great visual guides! 📚💪', NULL, '2025-04-19 13:00:56', '2025-04-19 13:00:56'),
(617, 15, 7, '📊 Anyone here using Grafana Loki with Promtail for logs? Thinking of switching from ELK stack. Would love feedback! 🔍📈', NULL, '2025-04-19 13:01:22', '2025-04-19 13:01:22'),
(618, 2, 7, '🧠✨ Just started experimenting with LangChain + OpenAI API to build a custom chatbot that can answer company-specific questions using internal docs. It’s insane how powerful it is when you combine embeddings + retrieval chains! 🤯 Anyone else diving into RAG (retrieval-augmented generation)? Would love to compare setups or tips. 🔍📚', NULL, '2025-04-19 13:02:10', '2025-04-19 13:02:10'),
(619, 2, 2, '💥⚙ FYI for the devs here: the latest Node.js release (v20.x) has some pretty sweet native features — built-in test runner, better support for ES modules, and faster async performance. If you\'re still on v16 or earlier, now\'s a good time to upgrade. 🚀 Let me know if you want a quick guide or checklist to make the switch smoothly! 👨💻📦', NULL, '2025-04-19 13:02:41', '2025-04-19 13:02:41'),
(620, 2, 2, '🌐 Anyone here played with Cloudflare Workers recently? I set up a serverless edge function in under 10 minutes — zero cold starts, and it’s blazing fast ⚡😳 Super useful for lightweight APIs or global redirects. Would love to hear if anyone’s used it in production and how it scales! 💬🧩', NULL, '2025-04-19 13:02:55', '2025-04-19 13:02:55'),
(621, 2, 2, '🔐 Heads-up for the security folks: I came across a new phishing attack pattern using AI-generated audio to mimic real voices in calls. Wild stuff 😨🎙 It’s getting harder to trust even a phone call now. Might be a good time to revisit internal security training & MFA policies 🔒📱', NULL, '2025-04-19 13:03:08', '2025-04-19 13:03:08'),
(622, 2, 2, '📊 Been setting up a real-time dashboard using Grafana + Prometheus for one of our microservices, and wow — the visibility it gives is next-level 🔍✨ Setting custom alerts on latency, CPU usage, and even 3rd party API failures has already saved us once. Happy to share the config if anyone\'s interested! 💡📈', NULL, '2025-04-19 13:03:22', '2025-04-19 13:03:22'),
(623, 2, 1, '🧩 Diving into event-driven architecture with Kafka lately, and it\'s such a different mindset from REST APIs. Feels more decoupled and scalable, but there’s definitely a learning curve 📉➡📈 Anyone using Kafka or NATS in prod? Curious how you handle schema evolution or retries 💬🔁', NULL, '2025-04-19 13:03:54', '2025-04-19 13:03:54'),
(624, 2, 1, '📱 PSA: If you’re on Android and using Samsung’s keyboard, be careful with the new clipboard history update — apparently some people are seeing data persist longer than expected 🕵♂ Might be worth reviewing your clipboard settings and checking privacy preferences 🔍📋', NULL, '2025-04-19 13:04:06', '2025-04-19 13:04:06'),
(625, 2, 1, '💬 Anyone following the progress of Rust in backend dev? I just rewrote a small Node.js service in Rust using Axum, and the speed and safety is 🔥🔥. Compile-time checks catch so many issues early, though I’ll admit the borrow checker still gives me nightmares sometimes 😅🧠', NULL, '2025-04-19 13:04:19', '2025-04-19 13:04:19'),
(626, 2, 1, '🤯 Just learned you can use SQLite as a full-fledged OLAP engine with extensions like DuckDB and SQLite extensions! Wild how flexible SQL tooling is getting lately. Makes me wonder how many lightweight data projects don’t even need Postgres anymore 🤔📉📊', NULL, '2025-04-19 13:04:32', '2025-04-19 13:04:32'),
(627, 2, 1, '🧪 Running some tests on multi-region deployments using Fly.io, and the results are pretty interesting. Global latency drops significantly when you deploy services closer to users, but syncing state is where it gets tricky 🌍📡 If you’re doing edge deployments, how are you handling session data or DB consistency?', NULL, '2025-04-19 13:04:45', '2025-04-19 13:04:45'),
(628, 11, 1, '🌍 VIBE NATION STORYTIME: Random World Facts That’ll Make You Laugh & Think Twice 😂📚✨\r\n\r\nAight fam, gather round the vibe circle… it’s time for a little story full of fun facts, foolishness, and good laughs. Let’s call it:\r\n\r\n\"The Day I Realized The World Is Just Vibing on Madness\" 😩🌎💫\r\n\r\nSo I was bored one night scrolling endlessly — you know that kind of scroll that starts on Instagram, passes through Wikipedia, and ends on “why do goats scream like humans?” 😭🐐\r\n\r\nAnd fam, I discovered some facts that had me weak. Like actual tears-in-my-eyes, this-world-is-a-joke type facts. Let me take you on that journey real quick:\r\n\r\n💡 Fact #1: Bananas are berries… but strawberries aren’t.\r\nWait. What?? 🍌✅🍓❌\r\nScience just decided to gaslight us. So banana = berry, but strawberry = fraud? Imagine going your whole life thinking strawberry is the OG berry only to find out it’s an imposter 😭\r\n\r\n💡 Fact #2: Octopuses have 3 hearts and blue blood.\r\n🧡🧡🧡\r\nThey’re literally alien-coded. Like, why does one creature need THREE hearts? Some of us are out here just trying to protect one and it’s already broken 😩💔 Meanwhile octopus be like: “Eh, I got 2 backups.” 😂\r\n\r\n💡 Fact #3: Wombat poop is cube-shaped.\r\n🧱💩\r\nI’m sorry WHAT? Who asked for Lego bricks in the wild? 😂 Imagine being the forest janitor and seeing tiny brown dice everywhere. Nature’s weird man.\r\n\r\n💡 Fact #4: A group of flamingos is called a “flamboyance.”\r\n💃🦩💅\r\nPlease. I need this energy in my life. I’m no longer part of a squad — I’m part of a flamboyance now. Don’t invite me to your gathering unless it’s giving feathers and extra-ness. Periodt. 😂🔥', NULL, '2025-04-19 13:11:07', '2025-04-19 13:11:07'),
(630, 11, 1, '💡 Fact #5: Cows have best friends and get stressed when they’re separated.\r\n🐮💕\r\nYou’re telling me cows have BFFs and here I am getting left on read? 💔😩 Let me just moo my feelings into the void.\r\n\r\n💡 Fact #6: Napoleon was attacked by a horde of bunnies.\r\nYes. The Napoleon Bonaparte. 🤺🐰\r\nApparently, he ordered a “bunny hunt” for fun, but they released too many rabbits — and instead of running away, the bunnies charged him and his men. 💀\r\nBro got jumped by Bugs Bunny’s ancestors. I can’t breathe 💀😂\r\n\r\n💡 Fact #7: Sloths can take up to a month to digest one meal.\r\n🐌➡️😴\r\nThat’s the energy I want. One plate of jollof rice and I’m good for 30 days. Budget-friendly metabolism. Lazy luxury. Sloth life for me tbh. 🛋️🍛💤\r\n\r\n💡 Fact #8: There’s a town in Norway where it’s illegal to die.\r\n❌⚰️\r\nYup. It’s too cold to bury anyone, so the government said, “Yeah no, just don’t die here please.” 😂\r\nImagine the ambulance saying, “We’re taking you to another town for... reasons.” 🚑⛔', NULL, '2025-04-19 13:12:38', '2025-04-19 13:12:38'),
(631, 11, 1, '💡 Fact #9: There’s a species of jellyfish that’s basically immortal.\r\n⏳❌\r\nIt just keeps resetting its life cycle when it gets old. Meanwhile we age like bread. 🥲 Jellyfish out here respawning like a GTA character.\r\n\r\n💡 Fact #10: And finally… goats have accents.\r\n🐐🗣️\r\nSo basically a goat from London and a goat from Jamaica wouldn’t sound the same. Can you imagine hearing one say “blehhh” with a British accent? 💂‍♂️😂\r\n“Oi mate, you got any grass innit?” I’m finished.\r\n\r\nNow here’s the real vibe: 🌊\r\n\r\nSometimes the world feels wild, chaotic, even heavy. But when you look a little closer, you realize — Earth is a big comedy show, and we’re all just vibing through the madness one meme at a time. 😅🌈\r\n\r\nSo next time you’re stressed, just remember:\r\n👉 There are wombats pooping cubes.\r\n👉 Napoleon got smoked by rabbits.\r\n👉 Flamingos be chillin’ in flamboyance.\r\n👉 And goats… might be out here speaking Pidgin.\r\n\r\n🎶 Vibe soundtrack suggestion:\r\n“Essence” by Wizkid ft. Tems, or some light lofi beats while you read this again for the 3rd time and giggle like you’re not supposed to be laughing at work. 🤫🎧💅\r\n\r\nIf you made it this far, drop a 🐐 or 🧠 in the chat and let the crew know you\'re now an elite member of the Random Fact Gang.\r\n\r\n#StayCurious #LaughSmall #VibesOnly #GoatTalk #NapoleonVsBunnies\r\n\r\n💚 Love & Laughter,\r\n— Your Vibe Storyteller Extraordinaire', NULL, '2025-04-19 13:12:43', '2025-04-19 13:12:43'),
(642, 2, 1, 'The Impact of Technology on Modern Education.\r\n\r\nThe impact of technology on modern education has been both profound and transformative, reshaping the way students learn and teachers instruct in classrooms around the world. Over the past few decades, technology has evolved from being a supplementary tool to a central component of educational systems. Computers, tablets, smartboards, and especially the internet have enabled a new level of interactivity, accessibility, and customization in education. For example, online learning platforms such as Khan Academy, Coursera, and edX offer students access to high-quality instructional content from prestigious institutions, often for free or at a minimal cost. This has broken down geographical and financial barriers, allowing individuals from remote or underserved regions to learn alongside their peers in more developed areas. In classrooms, digital tools facilitate a more engaging learning environment.\r\n', NULL, '2025-05-06 18:04:45', '2025-05-06 18:04:45'),
(656, 2, 2, 'There are many social media apps available worldwide. Major platforms include Facebook, YouTube, WhatsApp, Instagram, TikTok, Telegram, Snapchat, LinkedIn, Discord, and many others, each with hundreds of millions-or even billions-of users. In fact, there are over 40 widely used social media platforms, covering everything from messaging and video sharing to professional networking and niche communities. New apps also continue to launch regularly, offering even more options for users in 2025', NULL, '2025-05-08 18:58:25', '2025-05-08 18:58:25'),
(722, 2, 25, 'Here is an animated background that enhances user experience. I used CSS animations and HTML5 canvas to create this visually appealing effect that don’t affect performance.', NULL, '2025-05-31 14:29:35', '2025-05-31 14:29:35'),
(723, 2, 25, 'Particle Animation', NULL, '2025-05-31 14:30:18', '2025-05-31 14:30:18'),
(724, 2, 25, '<!DOCTYPE html>\n<html lang=\"en\">\n<head>\n    <meta charset=\"UTF-8\">\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n    <title>Login</title>\n    <link href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css\" rel=\"stylesheet\">\n    <link href=\"https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css\" rel=\"stylesheet\">\n    <style>\n        body {\n            margin: 0;\n            padding: 0;\n            height: 100vh;\n            overflow: hidden;\n            background-color: #2c3e50;\n            display: flex;\n            justify-content: center;\n            align-items: center;\n        }\n\n        .container {\n            position: absolute;\n            z-index: 10;\n            max-width: 400px;\n            padding: 20px;\n            background-color: rgba(255, 255, 255, 0.8);\n            border-radius: 10px;\n            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);\n        }', NULL, '2025-05-31 14:30:53', '2025-05-31 14:30:53'),
(725, 2, 25, '        h2 {\n            text-align: center;\n            color: #333;\n        }\n\n        button {\n            width: 100%;\n            background-color: #4c7dff;\n            color: white;\n            border: none;\n            padding: 12px;\n            margin-top: 15px;\n            font-size: 16px;\n            border-radius: 5px;\n            transition: background-color 0.3s;\n        }\n\n        button:hover {\n            background-color: #356ac3;\n        }\n\n        canvas {\n            position: absolute;\n            top: 0;\n            left: 0;\n            z-index: 1;\n        }\n    </style>\n</head>\n<body>\n    <canvas id=\"particles\"></canvas>\n    <div class=\"container\">\n        <h2>Login</h2>\n        <form action=\"login.php\" method=\"POST\">\n            <div class=\"form-group\">\n                <input type=\"email\" class=\"form-control\" id=\"email\" name=\"email\" required placeholder=\"Email\">\n            </div>', NULL, '2025-05-31 14:31:22', '2025-05-31 14:31:22'),
(726, 2, 25, '            <div class=\"form-group\">\n                <input type=\"password\" class=\"form-control\" id=\"password\" name=\"password\" required placeholder=\"Password\">\n            </div>\n            <button type=\"submit\"><i class=\"fas fa-sign-in-alt\"></i> Login</button>\n        </form>\n    </div>\n\n    <script>\n        const canvas = document.getElementById(\'particles\');\n        const ctx = canvas.getContext(\'2d\');\n\n        canvas.width = window.innerWidth;\n        canvas.height = window.innerHeight;\n\n        let particles = [];\n\n        class Particle {\n            constructor(x, y) {\n                this.x = x;\n                this.y = y;\n                this.size = Math.random() * 5 + 1;\n                this.speedX = Math.random() * 3 - 1.5;\n                this.speedY = Math.random() * 3 - 1.5;\n                this.color = \'rgba(255, 255, 255, 0.6)\';\n            }', NULL, '2025-05-31 14:32:04', '2025-05-31 14:32:04'),
(727, 2, 25, '            update() {\n                this.x += this.speedX;\n                this.y += this.speedY;\n                if (this.size > 0.2) this.size -= 0.1;\n            }\n\n            draw() {\n                ctx.fillStyle = this.color;\n                ctx.beginPath();\n                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);\n                ctx.fill();\n            }\n        }\n\n        function createParticles(e) {\n            let xPos = e.x;\n            let yPos = e.y;\n            for (let i = 0; i < 5; i++) {\n                particles.push(new Particle(xPos, yPos));\n            }\n        }\n\n        function animateParticles() {\n            ctx.clearRect(0, 0, canvas.width, canvas.height);\n            for (let i = 0; i < particles.length; i++) {\n                particles[i].update();\n                particles[i].draw();\n                if (particles[i].size <= 0.2) {\n                    particles.splice(i, 1);', NULL, '2025-05-31 14:32:24', '2025-05-31 14:32:24'),
(728, 2, 25, '                    i--;\r\n                }\r\n            }\r\n            requestAnimationFrame(animateParticles);\r\n        }\r\n\r\n        canvas.addEventListener(\'mousemove\', createParticles);\r\n        animateParticles();\r\n    </script>\r\n</body>\r\n</html>\r\n', NULL, '2025-05-31 14:32:49', '2025-05-31 14:32:49'),
(729, 2, 25, 'Explanation:\r\n•    Canvas Particles: The particles are dynamically created as circles that move in random directions and slowly fade away. This creates an engaging background effect.\r\n•    Interactive: The particles appear where the user moves their mouse over the screen, adding a layer of interactivity.\r\n•    Canvas Size: The canvas automatically adjusts to the full screen size.\r\n', NULL, '2025-05-31 14:33:08', '2025-05-31 14:33:08'),
(741, 2, 2, 'wow! this is so nice🎉', NULL, '2025-06-05 19:11:04', '2025-06-05 19:11:04'),
(742, 2, 25, 'Thank you', NULL, '2025-06-05 19:11:59', '2025-06-05 19:11:59'),
(743, 2, 2, 'Hey guys, have you seen the updates on AWS Bedrock? They\'re expanding support for more foundational models. Pretty cool stuff for anyone integrating generative AI into their apps.', NULL, '2025-06-05 19:15:49', '2025-06-05 19:15:49'),
(744, 2, 1, 'Yeah, I saw that! It\'s wild how fast the cloud platforms are adapting to GenAI. I’m personally leaning towards Azure OpenAI right now though — smoother integration with my .NET backend.', NULL, '2025-06-05 19:17:13', '2025-06-05 19:17:13'),
(745, 2, 2, 'Makes sense. I think AWS still dominates in flexibility, but Azure’s tight integration with Microsoft’s ecosystem is unbeatable. By the way, have you tried LangChain for managing AI pipelines?', NULL, '2025-06-05 19:18:01', '2025-06-05 19:18:01'),
(746, 2, 1, 'Yep, tried it last week. It’s great for chaining prompts and tools, but I’m still figuring out how to scale inference without latency issues. Any tips?', NULL, '2025-06-05 19:19:27', '2025-06-05 19:19:27'),
(747, 2, 2, 'Try using a hybrid approach — cache frequent requests locally, offload complex inference to the cloud, and use an LLM router to distribute between models. There’s also some new work on lightweight edge deployments with llama.cpp.', NULL, '2025-06-05 19:20:32', '2025-06-05 19:20:32'),
(748, 2, 1, 'Smart! I’ll experiment with that. Have you also looked into DevOps for ML (MLOps) recently? I\'m thinking of setting up a CI/CD pipeline for model updates.', NULL, '2025-06-05 19:21:57', '2025-06-05 19:21:57'),
(749, 2, 2, 'Definitely. GitHub Actions with Docker and MLflow is a solid combo. Also, check out Weights & Biases for experiment tracking — it’s a game changer.', NULL, '2025-06-05 19:22:48', '2025-06-05 19:22:48'),
(750, 2, 1, 'Awesome, will do! This group really needs a pinned post with resources — we drop too much gold here 😂', NULL, '2025-06-05 19:23:39', '2025-06-05 19:23:39'),
(751, 2, 2, '100%. I’ll start putting together a shared Notion or GitHub gist. Let’s build out a central “Tech Stack of the Month” post too.', NULL, '2025-06-05 19:24:35', '2025-06-05 19:24:35'),
(752, 2, 25, 'Definitely Topic on AI, cloud computing, and developer tools😁😁, prove me wrong!!', NULL, '2025-06-05 19:26:54', '2025-06-05 19:26:54'),
(821, 2, 50, 'Deleted user test', NULL, '2025-10-04 19:39:06', '2025-10-04 19:39:06');

-- --------------------------------------------------------

--
-- Table structure for table `message_reactions`
--

CREATE TABLE `message_reactions` (
  `id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reaction` varchar(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `message_reactions`
--

INSERT INTO `message_reactions` (`id`, `message_id`, `user_id`, `reaction`, `created_at`) VALUES
(10, 267, 1, '😂', '2025-05-19 12:15:20'),
(11, 267, 2, '😂', '2025-09-01 18:11:16');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `token_expiry` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `token`, `token_expiry`) VALUES
(1, 1, '9007607e91652e9c6df1f7c0353d1d6bf3ecc6bc14a85611afa341687e2ddd21', '2024-12-12 17:39:08'),
(2, 1, 'e0a1b7c9f4cfb583ef44c75a07b445c18ece71124df89de55b6dea3acce7f3b2', '2024-12-12 17:39:38'),
(3, 1, 'aefbdc91133299052666a2b857de8dc0a8ecf18e7b0d2d8249b39444a1b6ec3a', '2024-12-12 17:39:44'),
(4, 1, '8397225c51867edbc65995deffb04c8ba6245879a2d87935160752b56b47a462', '2024-12-12 17:39:49'),
(5, 1, 'b2eac35948e3f0c01fd2cdddd613d8fb4b0a914836b9776ff3706e251eb858c5', '2024-12-12 17:46:59'),
(6, 1, '114e4a0d64ae6b860e0ab82d21f2bda5d25f10a4a7e2eaecf9fbe05bfb6b1ae6', '2024-12-12 17:47:52'),
(7, 1, 'dab058b4f81670f186bfcc75506c32c0f57fe7d5dee0ad099f724e174e78c58f', '2024-12-12 17:48:06'),
(8, 1, 'e00248edd81956d7d75b3d618d3ca81f0ec6b4218711d7d9eacefe96ada0de37', '2024-12-12 17:51:15'),
(11, 2, '8d18834fb2fcb390ccea2c4b879eb49fd9e575cea51f39eefe69577902f052df', '2024-12-12 17:59:01'),
(12, 2, '166170f8e06bda6eceb521077e08026a049183954800adfb3072d6b1e7a72637', '2024-12-12 17:59:35'),
(14, 7, 'ee70be4cf9824e8e5ff8f87811f703c49bd56cd0165c89bdd1906d9a925b607a', '2024-12-16 17:30:06'),
(15, 1, 'a417d26d08b25ec7d415b85c907e5e522efbfcf29df2328ecaa74949cee279b2', '2024-12-16 17:32:03'),
(16, 1, '685c810f01b776c13f6768767518a6fe8786f6e5bec4d9e254301eca7f95cd2f', '2024-12-21 14:54:15'),
(17, 1, '3ce03509163e4fc575329dd3e3c40ef9c6826f49ecc8e2adba0ef2ba0e149938', '2024-12-21 14:54:34'),
(18, 1, '36a19d4693f0078792deb2bf5ff4889c03e671a124ed5e6b0a5830207245788e', '2024-12-21 14:56:27'),
(19, 1, 'a5d21ebc47a488274d168c1769641b9b66e26b999fc4586b98011f4f8e96ff4a', '2024-12-21 14:58:41'),
(20, 1, 'dd7c9f7b30c6e7de72f739e31dd8a91a896f04de004d47674dd109755812461d', '2024-12-21 14:58:57'),
(22, 1, '3e4c9024eb890dddf6301c06737b084046d63176da185b48ea7b41bb40ba6bf3', '2024-12-21 15:03:11'),
(23, 1, '6bfa018927944a87b43be2676a3ed407b25532dc63909641a738e0e32e52511a', '2024-12-21 15:03:17'),
(25, 1, 'fb48c00b04324692e4765ef113a0ae3f8fcf1c1308fb60310c78cfa732ac8774', '2024-12-21 17:43:29'),
(26, 1, '3bf92b8bb959b648ede96ad758fc5b1b797695ce5577f7a7af9d7592c1262c17', '2024-12-21 20:12:03'),
(27, 6, '0d2e523237997cd951a6e34418a81814d641fbc2ffb21c0ed578e14304d5b6e0', '2024-12-21 20:35:20'),
(28, 6, 'b5e1319f6b61adbbff71b550e0702e98def1094223318b667318af8697deea86', '2024-12-21 20:36:30'),
(29, 1, 'de5408838ca549639af11a3a2935f1ead06baa142157e39e0f9b080aa30f7a8e', '2024-12-22 11:33:16'),
(30, 1, '4987aefa50f563514e82cdc05d52e5ec0c0d09f29404c7a5ba99dae0b76e0a5b', '2024-12-22 11:35:18'),
(31, 1, '746c524924868bf922182f92221156790f2fa5397ed8028546ffd006e0020007', '2024-12-22 11:37:36'),
(32, 1, '1396ab4497b2a6db4b8c580a98050522bc54c815aeb9263124abdb46f2fb8c28', '2024-12-22 11:43:17'),
(33, 1, 'a77e774470ee0faa79b0d1ada1aeeb8354d00b8c06dac9721a5fa1df6016c1b6', '2024-12-22 11:43:28'),
(34, 1, '0c2071f60b66686f4e9376ee6e0c5bd004e354eb682d4b06f7ab5df3feb524ce', '2024-12-22 11:56:38'),
(35, 1, 'a00155c8c0168b21d5c1d7a0f34cb4340e47f3a875c89a5ee9c52f0997417482', '2024-12-22 11:57:41'),
(36, 1, 'e28af44872f5195764e9afc795407658fd63fe3a13acac45269f6d944ff2062d', '2024-12-22 11:57:46'),
(37, 1, '385c3caecb683dee65d0407bd28abc72c6e95d6a8c2eef3a30ea6fa2f6f2871f', '2024-12-22 11:59:10'),
(38, 1, 'a08cc2d172026b06752c9f98c926aed5435617b59a8775364cfe32b2185f5640', '2024-12-22 12:02:03'),
(39, 1, '6a04af2c983584fe31bf82bc9d24c2648e4cd08bee01f19e621969bd6fd4c005', '2024-12-22 12:02:18'),
(40, 1, '000af657c4d2f63c78f9b5e9c689e1f8f68f7bb1159f7352259d5ec1738d778a', '2024-12-22 12:02:54'),
(41, 1, '250169eafa3a6a636cc2021b15e201795b1923ea1fc25a45f5e160aa5a457bd6', '2024-12-22 12:03:57'),
(42, 1, '440597733fd0457e3d695ce410103c021f999a76e517f0f7e79f3f5f74425bd0', '2024-12-22 12:08:38'),
(43, 1, 'd6213c371c5de02480d8e1b52a7ade3e20269dfca487adff605ae2981c54a3ec', '2024-12-22 12:09:07'),
(44, 1, 'c0cf4f69e11ed12daacea8b58cf41000a82ee1a5f15a3a7bf97fba3e72026be5', '2024-12-22 12:11:14'),
(45, 1, '466b5b7f2a4f142759220fe584441b20d30f347118d45168cc4009228048e155', '2024-12-22 12:12:21'),
(46, 1, 'fb68d2a73c30963b1c0a8332f9a5485952c32c3000240e16f69416937a850e32', '2024-12-22 12:12:48'),
(47, 1, '28db87127c260a8d4f87e5fa301a38f55bede2c2becc2d0d50400748f3d707cf', '2024-12-22 12:13:39'),
(48, 1, '5e8b81f7a88652d9ead85c8cc3ef34bbabb2b88fbdddccc9713b96b1de38198d', '2024-12-22 12:14:21'),
(49, 1, '3d82e677753c77a94dec71840c4103b7d11ac55c4d20996e4daddbf51d1c8587', '2024-12-22 12:14:37'),
(50, 1, '7be29a510d189af3773b74462cbe6b4bc8ee8f187f473a853941e43f5dfff18c', '2024-12-22 12:16:04'),
(51, 1, '7e8a7d232eb3311c7248d9067ed785b820b969bbb9cd3fb656b6cae364f6400f', '2024-12-22 12:16:59'),
(52, 1, '4854101df3aeb09b395c80d84a6046fda7d4c3dfa3984a811d3731155ac238d4', '2024-12-22 12:26:48'),
(53, 1, '3a7cfab08d985df161f8733eec62b355e33e619e32cea8b64d3d05400582e3ca', '2024-12-22 12:28:30'),
(54, 1, 'ce5e2a408ef1163fbe1f170c7c4965d14eaf7e0114cb2f6de45529554c9a4d9b', '2024-12-22 12:30:02'),
(55, 1, '2db760d1e1137713689dcdf21f5c51d85e9a85f4d95569ecc14724624e496bdc', '2024-12-22 12:33:51'),
(56, 1, '5cb8866d2cfd0dfda9ff8d07a99190307fa5968731afdf15b2048388e11878f8', '2024-12-23 11:48:18'),
(59, 2, '05a1420c77afa82f18eff64c7940206c19dcecb6d0749bee6e7739198254b46f', '2024-12-23 17:43:12'),
(60, 1, 'fd18ec1baa29577f60c6cb7835133d21f5f7f9f37f968ab0814c88025f741af5', '2024-12-23 17:43:23'),
(61, 2, '9482dcd68160dbd5d99bd7a4a914f6dec96108534227e9714f15d0b7a70f5e77', '2024-12-23 18:21:46'),
(62, 1, 'dce0f313414b61d65758262c64eb71466534c2ac86b1aaa30e7428f6eb421969', '2024-12-23 18:23:54'),
(70, 2, 'e259abe061e796d5137cd03c760df705d1fe53ee3880c04537fbe677a0a2b4ed', '2025-03-13 15:50:40'),
(71, 1, '6898c35b63441628eb069958e22b0afbb9ec6f339ea43c7049fce5fd40eccdb7', '2025-05-06 15:25:39'),
(72, 1, '5802d3c1ece22d413e37f16adfee9b50db5c3d1eb4170476f6b3c0138a5264f8', '2025-05-06 15:25:51'),
(73, 1, '38c71d5eda79f25d293342690d33ec57f04382aa381fd0e44887e0e8cf558d97', '2025-05-08 16:23:31'),
(74, 2, '52809eccafba4f0fbf2edaabe110baf4125509ddc6f4039457da7cbc8218607d', '2025-05-16 23:57:13'),
(75, 2, '59a9c7e433fb7963ca942ff4f7e0c2d503f7456b392c7b6b47a209b9bc2a57b2', '2025-05-17 13:14:41'),
(77, 1, '3fe2496e3ba652185915a066def44f026ce0c35e441feeee7a019bcac2dcd6fd', '2025-05-19 14:46:41'),
(78, 6, 'd502bb8cabb9fcdd115d14fc7b2c37725a4d53507ecc58450f97a893240a16df', '2025-05-30 16:37:01'),
(79, 2, '26ccfd3e60a434963c5658cf03e86f71d6a1432f1def718004e506d65a5d97b1', '2025-06-05 18:03:29'),
(80, 2, '2f4ed820b2ee8b2605a77226ba88aba96af2376f29fcfdbb12715ff5943bf803', '2025-06-06 23:47:00'),
(81, 2, '620afda2ec4fb19d7f60e0784138a462fe350d2c887bcf2ef41ca3eba2b463bf', '2025-06-11 17:47:49'),
(83, 2, '7028c63c041644695b166f37136c17fac107d0debf449d34df9ca187a813ca38', '2025-06-11 17:52:14'),
(84, 2, '2c52f9fbe06f91aebcbf2ae5af800f8d864701572343a5020a2226f2cfafd311', '2025-06-11 17:53:39'),
(85, 2, '256a0c08080ec97687e2823b4d365e17921ecbfcbccfa80e58bd9f87170e3c0f', '2025-06-11 17:56:01'),
(86, 2, '12b042ac545a1b4b885139bcc4316dbadaf1ecd6d646f372f54f36c74051f3b4', '2025-09-07 18:41:40'),
(87, 2, '2e20e802e253123bf504cd36045cca5fe3b4484f18e69868e8ffcc0400196c23', '2025-09-30 14:03:38'),
(88, 2, 'd91ef8a950640e2ce9f3c86b9fab174fc1ed518fd14f84af3396e43b1f705a45', '2025-10-03 13:42:02'),
(89, 2, '6842049a5426920a19b5a467afea6a33346708bd666beffeb45f3a9c40009231', '2025-10-06 11:46:26'),
(90, 2, 'b32c88a1d8ae9d569f3b9ba72be23660c08ca992f1897ee545ffe027117c39e7', '2025-10-08 13:02:46'),
(91, 2, '77f0b20952a226cb83552a8ae440fef6b70044555cf01f740a8e0192ac9dfa10', '2025-10-08 13:12:46'),
(92, 2, '6f24a46b3f7a575c1e988c750a5e96b2eccf31da07e51446670f0b6d1fb0d3e6', '2025-10-08 13:31:43');

-- --------------------------------------------------------

--
-- Table structure for table `private_messages`
--

CREATE TABLE `private_messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `file_path` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0,
  `reply_to` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `private_messages`
--

INSERT INTO `private_messages` (`id`, `sender_id`, `receiver_id`, `content`, `file_path`, `created_at`, `is_read`, `reply_to`) VALUES
(267, 2, 1, 'Hey, So, I tried to cook a new recipe last night. It was supposed to be a fancy risotto, but it ended up looking like… a science experiment gone wrong.', NULL, '2025-05-19 12:14:41', 1, NULL),
(268, 1, 2, 'It sounds like a great candidate for a food blog. You could call it “Cooking Catastrophes: How Not to Use a Stove.', NULL, '2025-05-19 12:16:57', 1, NULL),
(269, 2, 1, 'Ha ha, very funny. Actually, I think I might’ve left out a crucial step. Like the step where you actually add the ingredients.', NULL, '2025-05-19 12:17:40', 1, NULL),
(270, 1, 2, 'You forgot the ingredients? I once forgot to turn on the oven. My cookies were just… well, dough.', NULL, '2025-05-19 12:18:51', 1, NULL),
(271, 2, 1, 'At least I didn’t set off the fire alarm. Last time I cooked, I tried to make toast and ended up with a charred kitchen.', NULL, '2025-05-19 12:19:47', 1, NULL),
(272, 1, 2, 'Charred toast sounds like a breakfast from “Survivor: The Kitchen Edition.”', NULL, '2025-05-19 12:20:39', 1, NULL),
(273, 1, 2, 'You know, we should have a cooking challenge. Each of us makes something and we all taste test. The winner gets bragging rights and the loser has to clean up.', NULL, '2025-05-19 12:21:21', 1, NULL),
(274, 1, 2, 'And the loser also has to post a photo of their creation on social media with the caption: “Gourmet masterpiece or kitchen horror?”', NULL, '2025-05-19 12:21:46', 1, NULL),
(275, 2, 1, 'Challenge accepted. But if I win, I want the prize to be a fire extinguisher.', NULL, '2025-05-19 12:22:50', 1, NULL),
(276, 2, 1, 'Or if it tastes good, we’ll just call it “smoky flavor.”', NULL, '2025-05-19 12:23:14', 1, NULL),
(277, 2, 1, 'Here’s to cooking disasters and kitchen triumphs!', NULL, '2025-05-19 12:23:59', 1, NULL),
(403, 2, 6, 'TEST', '[{\"path\":\"uploads\\/68e29d4515fa3.png\",\"name\":\"Screenshot 2024-12-12 162010.png\",\"type\":\"image\\/png\"}]', '2025-10-05 16:45:53', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `about` text NOT NULL DEFAULT 'Hi there! I am using this chat application.',
  `profile_picture` varchar(255) NOT NULL DEFAULT 'profile/default_profile.jpg',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_activity` datetime DEFAULT NULL,
  `is_online` tinyint(1) DEFAULT 0,
  `is_typing` tinyint(1) DEFAULT 0,
  `typing_to` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `about`, `profile_picture`, `created_at`, `last_activity`, `is_online`, `is_typing`, `typing_to`) VALUES
(1, 'MegaTron', 'megatron@gmail.com', '$2y$10$h7.mrrFzm049DbW3EA2vR.hOQNKkJuWIkFRNDuvpgt.aQ83OKBB7q', 'COOLFLIX°', 'uploads/profile_6a9d68c167075.jpg', '2024-12-09 00:22:37', '2025-10-06 18:37:44', 0, 0, NULL),
(2, 'ADMIN', 'admin@admin.com', '$2y$10$EuJ7xsvyKfmHUTr7VCw/4eLLK6oLI5aCxO6oD7Vab77NJ513LD.pS', 'Tech enthusiast', 'uploads/profile_6a9d686bd2a00.jpg', '2024-12-09 00:22:37', '2026-09-03 23:50:28', 0, 0, NULL),
(6, 'Vintage', 'vintage@gmail.com', '$2y$10$vOq/M0rbYhiuzmJYEusZXuzIO0sH90x9OmdyDva0OVxWFBt3BgRVq', 'VinTech®', 'uploads/70a0dc5f182148158516acccc0b1699b.jpg', '2024-12-09 00:22:37', '2025-05-31 14:06:04', 0, 0, NULL),
(7, 'BambleBee', 'bamblebee@gmail.com', '$2y$10$hBdP97aRqyO/YFD179ozH.foTCnbj3On.sOkt3gHwLZqz7cMMuj7q', 'The Tech Transformer', 'uploads/9196bbae524241bda0560a77f2c0b063.jpg', '2024-12-09 00:22:37', NULL, 0, 0, NULL),
(10, 'Matrix', 'matrix@gmail.com', '$2y$10$rRspk1az1jveKSlVaVGZc.Ge1Vi46roXTQ857IvmK7RdUt84U5KfC', 'No matrix escape', 'uploads/download (1).jpg', '2024-12-09 00:22:37', NULL, 0, 0, NULL),
(15, 'DeletedUser_2966293a6067b0e4', 'deleted_f7ad4db1c853cef9@deleted.local', '', 'This account has been deleted.', 'profile/deleted_user.jpg', '2024-12-09 00:22:37', NULL, 0, 0, NULL),
(19, 'CyberTron', 'cybertron@gmail.com', '$2y$10$FoUuPGiiglpygMzPShneqODFBBfmKd8jcdfBAlXCTs05fxNH4vrby', 'Hi there! I am using this chat application.', 'uploads/profile_6a9d6bbe81e51.jpg', '2024-12-09 00:22:37', NULL, 0, 0, NULL),
(21, 'Ultron~Ultron', 'ultron@gmail.com', '$2y$10$YsyymrUKX6zuJl2ymqLbJeEtSbnMCrt0S5yiT1LRZQbdU08iX3MDe', 'Hi there! I am using this chat application.', 'uploads/profile_6a9d6c3203304.jpg', '2024-12-09 00:22:37', NULL, 0, 0, NULL),
(25, 'Meta', 'meta@gmail.com', '$2y$10$qNdE8YxBjwgGMqbA9MjXIOcwpsKhvPAce7lDlB6sCjs0v1buMvDi2', 'Official Me', 'uploads/profile_683ae6d51fba5.png', '2024-12-09 10:13:06', '2025-06-05 19:38:55', 0, 0, NULL),
(50, 'DeletedUser_6e03502b3dcbb7b9', 'deleted_13d22f7a385a1790@deleted.local', '', 'This account has been deleted.', 'profile/deleted_user.jpg', '2025-10-04 16:34:18', NULL, 0, 0, NULL),
(51, 'DeletedUser_39845d888562ef80', 'deleted_658cacff2b20ab3c@deleted.local', '', 'This account has been deleted.', 'profile/deleted_user.jpg', '2026-08-31 17:53:54', NULL, 0, 0, NULL),
(52, 'DeletedUser_5ef00722e1ac5fb2', 'deleted_a7893e79ec9d1c67@deleted.local', '', 'This account has been deleted.', 'profile/deleted_user.jpg', '2026-09-02 14:04:22', NULL, 0, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_cleared_messages`
--

CREATE TABLE `user_cleared_messages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `cleared_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `fk_creator` (`creator_id`);

--
-- Indexes for table `group_users`
--
ALTER TABLE `group_users`
  ADD PRIMARY KEY (`group_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_group` (`group_id`),
  ADD KEY `fk_user` (`user_id`);

--
-- Indexes for table `message_reactions`
--
ALTER TABLE `message_reactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `message_id` (`message_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `private_messages`
--
ALTER TABLE `private_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_cleared_messages`
--
ALTER TABLE `user_cleared_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `message_id` (`message_id`),
  ADD KEY `group_id` (`group_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=130;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=886;

--
-- AUTO_INCREMENT for table `message_reactions`
--
ALTER TABLE `message_reactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT for table `private_messages`
--
ALTER TABLE `private_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=411;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `user_cleared_messages`
--
ALTER TABLE `user_cleared_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `groups`
--
ALTER TABLE `groups`
  ADD CONSTRAINT `fk_creator` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `groups_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `group_users`
--
ALTER TABLE `group_users`
  ADD CONSTRAINT `group_users_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`),
  ADD CONSTRAINT `group_users_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `fk_group` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`),
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `message_reactions`
--
ALTER TABLE `message_reactions`
  ADD CONSTRAINT `message_reactions_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `private_messages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `message_reactions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `private_messages`
--
ALTER TABLE `private_messages`
  ADD CONSTRAINT `private_messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `private_messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `user_cleared_messages`
--
ALTER TABLE `user_cleared_messages`
  ADD CONSTRAINT `user_cleared_messages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_cleared_messages_ibfk_2` FOREIGN KEY (`message_id`) REFERENCES `messages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_cleared_messages_ibfk_3` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
