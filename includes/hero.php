<?php
/**
 * Dynamic Hero Section Generator
 * Provides dramatic hero content based on page context and database data
 */

class HeroGenerator {
    private $pdo;
    private $defaultHero = [
        'title' => 'L1J Database',
        'subtitle' => 'Your comprehensive source for Lineage items, weapons, armor, and more',
        'background' => SITE_URL . '/assets/img/favicon/hero_bg.png'
    ];
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Generate hero content based on page type and data
     */
    public function getHero($page = 'home', $data = null) {
        switch($page) {
            case 'weapons':
                return $this->getWeaponsHero($data);
            case 'armor':
                return $this->getArmorHero($data);
            case 'items':
                return $this->getItemsHero($data);
            case 'dolls':
                return $this->getDollsHero($data);
            case 'maps':
                return $this->getMapsHero($data);
            case 'monsters':
                return $this->getMonstersHero($data);
            case 'weapon_detail':
                return $this->getWeaponDetailHero($data);
            case 'armor_detail':
                return $this->getArmorDetailHero($data);
            case 'admin':
                return $this->getAdminHero($data);
            default:
                return $this->getHomeHero();
        }
    }
    
    private function getHomeHero() {
        $stats = $this->getDatabaseStats();
        return [
            'title' => 'L1J Database',
            'subtitle' => sprintf(
                'Explore %s weapons, %s armor pieces, %s items, %s magic dolls and more',
                number_format($stats['weapons']),
                number_format($stats['armor']),
                number_format($stats['items']),
                number_format($stats['dolls'])
            ),
            'background' => SITE_URL . '/assets/img/favicon/hero_bg.png'
        ];
    }
    
    private function getWeaponsHero($data = null) {
        $count = $this->getTableCount('weapon');
        return [
            'title' => 'Weapon Arsenal',
            'subtitle' => sprintf('Master the battlefield with %s legendary weapons', number_format($count)),
            'background' => SITE_URL . '/assets/img/favicon/hero_bg.png'
        ];
    }
    
    private function getArmorHero($data = null) {
        $count = $this->getTableCount('armor');
        return [
            'title' => 'Armor Forge',
            'subtitle' => sprintf('Defend yourself with %s protective gear sets', number_format($count)),
            'background' => SITE_URL . '/assets/img/favicon/hero_bg.png'
        ];
    }
    
    private function getItemsHero($data = null) {
        $count = $this->getTableCount('etcitem');
        return [
            'title' => 'Item Vault',
            'subtitle' => sprintf('Discover %s essential items and consumables', number_format($count)),
            'background' => SITE_URL . '/assets/img/favicon/hero_bg.png'
        ];
    }
    
    private function getDollsHero($data = null) {
        $count = $this->getTableCount('magicdoll_info');
        return [
            'title' => 'Magic Dolls',
            'subtitle' => sprintf('Summon %s powerful companions to aid your journey', number_format($count)),
            'background' => SITE_URL . '/assets/img/favicon/hero_bg.png'
        ];
    }
    
    private function getMapsHero($data = null) {
        $count = $this->getTableCount('mapids');
        return [
            'title' => 'World Atlas',
            'subtitle' => sprintf('Navigate through %s realms and territories', number_format($count)),
            'background' => SITE_URL . '/assets/img/favicon/hero_bg.png'
        ];
    }
    
    private function getMonstersHero($data = null) {
        $count = $this->getTableCount('npc');
        return [
            'title' => 'Bestiary',
            'subtitle' => sprintf('Face %s creatures across the lands', number_format($count)),
            'background' => SITE_URL . '/assets/img/favicon/hero_bg.png'
        ];
    }
    
    private function getWeaponDetailHero($weapon) {
        if (!$weapon) return $this->defaultHero;
        
        $gradeText = $this->getGradeText($weapon['itemGrade'] ?? 'NORMAL');
        return [
            'title' => htmlspecialchars($weapon['desc_kr'] ?? $weapon['desc_en'] ?? 'Unknown Weapon'),
            'subtitle' => sprintf('%s %s - Unleash devastating power', $gradeText, ucfirst(strtolower($weapon['type'] ?? 'weapon'))),
            'background' => SITE_URL . '/assets/img/favicon/hero_bg.png'
        ];
    }
    
    private function getArmorDetailHero($armor) {
        if (!$armor) return $this->defaultHero;
        
        $gradeText = $this->getGradeText($armor['itemGrade'] ?? 'NORMAL');
        return [
            'title' => htmlspecialchars($armor['desc_kr'] ?? $armor['desc_en'] ?? 'Unknown Armor'),
            'subtitle' => sprintf('%s %s - Impenetrable defense awaits', $gradeText, ucfirst(strtolower($armor['type'] ?? 'armor'))),
            'background' => SITE_URL . '/assets/img/favicon/hero_bg.png'
        ];
    }
    
    private function getAdminHero($data = null) {
        $username = $_SESSION['username'] ?? 'Administrator';
        return [
            'title' => 'Command Center',
            'subtitle' => sprintf('Welcome back, %s. Database management at your fingertips.', htmlspecialchars($username)),
            'background' => SITE_URL . '/assets/img/favicon/hero_bg.png'
        ];
    }
    
    /**
     * Get database statistics
     */
    private function getDatabaseStats() {
        $stats = ['weapons' => 0, 'armor' => 0, 'items' => 0, 'dolls' => 0];
        
        try {
            $stats['weapons'] = $this->getTableCount('weapon');
            $stats['armor'] = $this->getTableCount('armor');
            $stats['items'] = $this->getTableCount('etcitem');
            $stats['dolls'] = $this->getTableCount('magicdoll_info');
        } catch(PDOException $e) {
            // Return defaults on error
        }
        
        return $stats;
    }
    
    /**
     * Get count from table
     */
    private function getTableCount($table) {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM `{$table}`");
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch(PDOException $e) {
            return 0;
        }
    }
    
    /**
     * Convert grade enum to display text
     */
    private function getGradeText($grade) {
        $grades = [
            'ONLY' => 'Unique',
            'MYTH' => 'Mythical',
            'LEGEND' => 'Legendary',
            'HERO' => 'Heroic',
            'RARE' => 'Rare',
            'ADVANC' => 'Advanced',
            'NORMAL' => 'Common'
        ];
        
        return $grades[$grade] ?? 'Common';
    }
    
    /**
     * Render hero HTML
     */
    public function render($page = 'home', $data = null) {
        $hero = $this->getHero($page, $data);
        
        echo '<section class="hero" style="background-image: url(\'' . htmlspecialchars($hero['background']) . '\');">';
        echo '<div class="hero-content">';
        echo '<h1>' . $hero['title'] . '</h1>';
        echo '<p>' . $hero['subtitle'] . '</p>';
        echo '</div>';
        echo '</section>';
    }
}

/**
 * Initialize global hero generator
 */
$heroGenerator = new HeroGenerator($pdo);

/**
 * Helper function for easy hero rendering
 */
function renderHero($page = 'home', $customTitle = null, $customSubtitle = null) {
    global $heroGenerator;
    if ($customTitle && $customSubtitle) {
        // Custom hero for specific items
        echo '<section class="hero" style="background-image: url(\'' . SITE_URL . '/assets/img/favicon/hero_bg.png\');">';
        echo '<div class="hero-content">';
        echo '<h1>' . htmlspecialchars($customTitle) . '</h1>';
        echo '<p>' . htmlspecialchars($customSubtitle) . '</p>';
        echo '</div>';
        echo '</section>';
    } else {
        $heroGenerator->render($page);
    }
}
?>
