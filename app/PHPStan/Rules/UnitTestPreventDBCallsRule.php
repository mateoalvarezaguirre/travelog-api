<?php

declare(strict_types=1);

namespace App\PHPStan\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<Node\Expr>
 */
final class UnitTestPreventDBCallsRule implements Rule
{
    private const FORBIDDEN_STATIC_METHODS = [
        'create',
        'find',
        'findOrFail',
        'first',
        'firstOrCreate',
        'firstOrNew',
        'updateOrCreate',
        'where',
        'all',
        'query',
        'insert',
        'update',
        'delete',
        'destroy',
        'truncate',
    ];

    private const FORBIDDEN_INSTANCE_METHODS = [
        'save',
        'delete',
        'update',
        'refresh',
        'load',
        'fresh',
    ];

    public function __construct(
        private readonly ReflectionProvider $reflectionProvider,
    ) {}

    public function getNodeType(): string
    {
        return Node\Expr::class;
    }

    /**
     * @return list<RuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $this->isUnitTestFile($scope)) {
            return [];
        }

        if ($node instanceof StaticCall) {
            return $this->checkStaticCall($node, $scope);
        }

        if ($node instanceof MethodCall) {
            return $this->checkMethodCall($node, $scope);
        }

        return [];
    }

    private function isUnitTestFile(Scope $scope): bool
    {
        $filePath = $scope->getFile();

        return str_contains($filePath, 'tests/Unit/');
    }

    /**
     * @return list<RuleError>
     */
    private function checkStaticCall(StaticCall $node, Scope $scope): array
    {
        if (! $node->name instanceof Identifier) {
            return [];
        }

        $methodName = $node->name->toString();

        if (! in_array($methodName, self::FORBIDDEN_STATIC_METHODS, true)) {
            return [];
        }

        $calledOnClass = $node->class;

        if (! $calledOnClass instanceof Node\Name) {
            return [];
        }

        $className = $scope->resolveName($calledOnClass);

        if (! $this->isEloquentModel($className)) {
            return [];
        }

        return [
            RuleErrorBuilder::message(sprintf(
                'Unit tests must not call database methods. Found %s::%s().',
                $className,
                $methodName,
            ))->identifier('unitTest.forbiddenDbCall')->line($node->getStartLine())->build(),
        ];
    }

    /**
     * @return list<RuleError>
     */
    private function checkMethodCall(MethodCall $node, Scope $scope): array
    {
        if (! $node->name instanceof Identifier) {
            return [];
        }

        $methodName = $node->name->toString();

        if (! in_array($methodName, self::FORBIDDEN_INSTANCE_METHODS, true)) {
            return [];
        }

        $callerType        = $scope->getType($node->var);
        $referencedClasses = $callerType->getReferencedClasses();

        foreach ($referencedClasses as $className) {
            if ($this->isEloquentModel($className)) {
                return [
                    RuleErrorBuilder::message(sprintf(
                        'Unit tests must not call database methods. Found %s->%s().',
                        $className,
                        $methodName,
                    ))->identifier('unitTest.forbiddenDbCall')->line($node->getStartLine())->build(),
                ];
            }
        }

        return [];
    }

    private function isEloquentModel(string $className): bool
    {
        if (! $this->reflectionProvider->hasClass($className)) {
            return false;
        }

        $classReflection = $this->reflectionProvider->getClass($className);

        return $classReflection->isSubclassOf(\Illuminate\Database\Eloquent\Model::class);
    }
}
