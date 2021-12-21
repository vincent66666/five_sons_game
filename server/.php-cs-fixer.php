<?php

$header = <<<'EOF'
@Created by PhpStorm
@User    : 清风醉
EOF;

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR2'                                  => true,
        '@Symfony'                               => true,
        '@DoctrineAnnotation'                    => true,
        '@PhpCsFixer'                            => true,
        'header_comment'                         => [
            'comment_type' => 'PHPDoc',
            'header'       => $header,
            'separate'     => 'none',
            'location'     => 'after_declare_strict',
        ],
        'array_syntax'                           => [
            'syntax' => 'short',
        ],
        'list_syntax'                            => [
            'syntax' => 'short',
        ],
        'concat_space'                           => [
            'spacing' => 'one',
        ],
        'blank_line_before_statement'            => [
            'statements' => [
                'declare',
            ],
        ],
        'general_phpdoc_annotation_remove'       => [
            'annotations' => [
                'author',
            ],
        ],
        'ordered_imports'                        => [
            'imports_order'  => [
                'class',
                'function',
                'const',
            ],
            'sort_algorithm' => 'alpha',
        ],
        'single_line_comment_style'              => [
            'comment_types' => [
            ],
        ],
        'yoda_style'                             => [
            'always_move_variable' => false,
            'equal'                => false,
            'identical'            => false,
        ],
        'phpdoc_align'                           => [
            'align' => 'left',
        ],
        'multiline_whitespace_before_semicolons' => [
            'strategy' => 'no_multi_line',
        ],
        'constant_case'                          => [
            'case' => 'lower',
        ],
        'class_attributes_separation'            => true,
        'declare_strict_types'                   => true,
        'linebreak_after_opening_tag'            => true,
        'lowercase_static_reference'             => true,
        'not_operator_with_successor_space'      => true,
        'not_operator_with_space'                => false,
        'ordered_class_elements'                 => true,
        'php_unit_strict'                        => false,
        'combine_consecutive_unsets'             => true, // 当多个 unset 使用的时候，合并处理
        'no_useless_else'                        => true, // 删除没有使用的else节点
        'no_unused_imports'                      => true, //删除没用到的use
        'phpdoc_separation'                      => false,// 不同注释部分按照单空行隔开
        'single_quote'                           => true, //简单字符串应该使用单引号代替双引号；
        'standardize_not_equals'                 => true, //使用 <> 代替 !=；
        'multiline_comment_opening_closing'      => true,
        'array_indentation'                      => true, // 数组的每个元素必须缩进一次
        'binary_operator_spaces'                 => ['default' => 'align_single_space'],//等号对齐、数字箭头符号对齐
        'no_useless_return'                      => true, // 删除没有使用的return语句
        'no_superfluous_phpdoc_tags'             => false,// 删除没有提供有效信息的@param和@return注解
        'no_empty_statement'                     => true, //多余的分号
        'no_leading_namespace_whitespace'        => true, //命名空间前面不应该有空格
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->exclude('public')
            ->exclude('runtime')
            ->exclude('taobao')
            ->exclude('vendor')
            ->in(__DIR__)
    )
    ->setUsingCache(false);
