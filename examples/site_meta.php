<?php

/**
 * SiteMeta - 站点元信息管理类
 * 
 * 用于存储网站的基础元信息，并提供描述文本生成功能。
 * 通过关联数组组织数据，支持多语言与简短摘要输出。
 */

class SiteMeta {
    
    /**
     * 站点元信息数组
     *
     * @var array
     */
    private $meta = [];
    
    /**
     * 构造函数，初始化默认元信息
     *
     * @param array $customMeta 可选的自定义元信息覆盖
     */
    public function __construct(array $customMeta = []) {
        $defaultMeta = [
            'site_name'        => '爱游戏平台',
            'site_url'         => 'https://index-main-i-game.com.cn',
            'description'      => '专注于优质游戏体验，爱游戏为您提供最新最热的游戏资讯与资源。',
            'keywords'         => ['爱游戏', '游戏资讯', '热门游戏', '游戏资源'],
            'author'           => 'Game Studio',
            'language'         => 'zh-CN',
            'version'          => '1.0.0',
            'creation_date'    => '2024-03-01',
        ];
        
        // 合并自定义元信息，保留默认值
        $this->meta = array_merge($defaultMeta, $customMeta);
    }
    
    /**
     * 获取单个元信息字段
     *
     * @param string $key 字段名称
     * @return mixed|null 值或null
     */
    public function getMeta(string $key) {
        return $this->meta[$key] ?? null;
    }
    
    /**
     * 设置或更新元信息字段
     *
     * @param string $key   字段名称
     * @param mixed  $value 字段值
     */
    public function setMeta(string $key, $value): void {
        $this->meta[$key] = $value;
    }
    
    /**
     * 生成简短描述文本（用于SEO或页面摘要）
     * 
     * 格式：站点名 - 描述 (关键词)
     * 对HTML实体进行转义以保证输出安全
     *
     * @return string 转义后的描述文本
     */
    public function generateShortDescription(): string {
        $name = htmlspecialchars($this->meta['site_name'] ?? '', ENT_QUOTES, 'UTF-8');
        $desc = htmlspecialchars($this->meta['description'] ?? '', ENT_QUOTES, 'UTF-8');
        $keywords = $this->meta['keywords'] ?? [];
        
        // 取前三个关键词，用逗号拼接
        $keywordStr = '';
        if (!empty($keywords)) {
            $selected = array_slice($keywords, 0, 3);
            $escaped = array_map(function($kw) {
                return htmlspecialchars($kw, ENT_QUOTES, 'UTF-8');
            }, $selected);
            $keywordStr = implode(', ', $escaped);
        }
        
        // 构建描述文本
        $description = $name . ' - ' . $desc;
        if (!empty($keywordStr)) {
            $description .= ' (' . $keywordStr . ')';
        }
        
        return $description;
    }
    
    /**
     * 返回完整元信息数组（已转义，可直接输出到HTML）
     *
     * @return array
     */
    public function getEscapedMeta(): array {
        $escaped = [];
        foreach ($this->meta as $key => $value) {
            if (is_array($value)) {
                $escaped[$key] = array_map(function($item) {
                    return htmlspecialchars((string)$item, ENT_QUOTES, 'UTF-8');
                }, $value);
            } elseif (is_string($value)) {
                $escaped[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            } else {
                $escaped[$key] = $value;
            }
        }
        return $escaped;
    }
    
    /**
     * 将元信息以HTML meta标签形式输出（示例用途）
     *
     * @return string HTML片段
     */
    public function toHtmlMetaTags(): string {
        $html = '';
        $escaped = $this->getEscapedMeta();
        
        if (isset($escaped['description'])) {
            $html .= '<meta name="description" content="' . $escaped['description'] . '" />' . "\n";
        }
        if (isset($escaped['keywords']) && is_array($escaped['keywords'])) {
            $kwString = implode(', ', $escaped['keywords']);
            $html .= '<meta name="keywords" content="' . $kwString . '" />' . "\n";
        }
        if (isset($escaped['author'])) {
            $html .= '<meta name="author" content="' . $escaped['author'] . '" />' . "\n";
        }
        if (isset($escaped['site_url'])) {
            $html .= '<link rel="canonical" href="' . $escaped['site_url'] . '" />' . "\n";
        }
        
        return $html;
    }
}

// ========== 使用示例 ==========

// 创建默认元信息实例
$site = new SiteMeta();

// 获取简短描述
echo "简短描述: " . $site->generateShortDescription() . "\n\n";

// 获取单个字段
echo "站点名称: " . $site->getMeta('site_name') . "\n";
echo "站点URL: " . $site->getMeta('site_url') . "\n\n";

// 自定义元信息测试（覆盖部分字段）
$custom = new SiteMeta([
    'site_name'   => '我的爱游戏站',
    'keywords'    => ['爱游戏', '游戏下载', '攻略'],
    'description' => '一个充满爱的游戏社区，爱游戏与你同在。',
]);

echo "自定义站点描述: " . $custom->generateShortDescription() . "\n\n";

// 输出HTML meta标签示例
echo "HTML meta标签:\n";
echo $custom->toHtmlMetaTags();

// 获取完整转义数组
$allMeta = $custom->getEscapedMeta();
echo "\n完整元信息数组:\n";
print_r($allMeta);