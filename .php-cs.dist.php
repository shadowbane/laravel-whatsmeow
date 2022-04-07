<?php

$finder = PhpCsFixer\Finder::create()
    ->notPath('vendor')
    ->notPath('bootstrap')
    ->notPath('storage')
    ->notPath('public')
    ->notPath('test')
    ->in(__DIR__)
    ->name('*.php')
    ->notName('*.blade.php');

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'no_unused_imports' => true,
        'trailing_comma_in_multiline' => true,
        'unary_operator_spaces' => true,
        'binary_operator_spaces' => true,
        'phpdoc_add_missing_param_annotation' => ['only_untyped' => false],
        'phpdoc_align' => false,
        'phpdoc_order' => true,
        'phpdoc_annotation_without_dot' => true,
        'no_blank_lines_after_phpdoc' => true,
        'blank_line_before_statement' => [
            'statements' => ['break', 'continue', 'declare', 'return', 'throw', 'try'],
        ],
        'method_argument_space' => [
            'on_multiline' => 'ensure_fully_multiline',
            'keep_multiple_spaces_after_comma' => true,
        ],
        'no_trailing_whitespace_in_comment' => true,
        'native_function_casing' => true,
        'phpdoc_no_package' => false,
        'yoda_style' => false,
        'no_superfluous_phpdoc_tags' => false,
        'single_trait_insert_per_statement' => true,
    ])
    ->setFinder($finder);
