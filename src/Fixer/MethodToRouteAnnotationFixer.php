<?php

namespace AppBundle\Fixer;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Tokens;

final class MethodToRouteAnnotationFixer extends AbstractFixer
{
    public function getName()
    {
        return 'App/'.parent::getName();
    }

    public function getDefinition()
    {
        return new FixerDefinition(
            'Replace @Method annotation by @Route(..., methods={}).',
            [
                new CodeSample(
                    '<?php 
/**
 * @Route("/hello-world", name="hello_world")
 * @Method("GET")
 */
public function helloWorldAction(){
//...
}                          
'
                ),
            ]
        );
    }

    public function isRisky()
    {
        return false;
    }

    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAllTokenKindsFound([\T_DOC_COMMENT]);
    }

    public function supports(\SplFileInfo $file)
    {
        return preg_match('/Controller$/', $file->getBasename('.php'));
    }

    public function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(\T_DOC_COMMENT)) {
                continue;
            }

            $content = $token->getContent();

            if (false !== strpos($content, '@Route') && false !== strpos($content, '@Method')) {
                $methodAnnotationStart = strpos($content, '@Method');
                $methodAnnotationEnd = strpos($content, ')', $methodAnnotationStart) + 1;
                $methodsToMove = $this->getMethodsToMove(substr($content, $methodAnnotationStart, $methodAnnotationEnd - $methodAnnotationStart));
                $newContent = $this->removeMethodLine($content, $methodAnnotationEnd);
                $newContent = $this->addMethodArgInRoute($newContent, $methodsToMove);
                $token->setContent($newContent);
            }
        }
    }

    private function getMethodsToMove(string $methodLine)
    {
        $methodLine = str_replace('|', ',', $methodLine);
        $methodLine = str_replace('"', '', $methodLine);
        $methodLine = str_replace('\'', '', $methodLine);
        $methodLine = str_replace('{', '', $methodLine);
        $methodLine = str_replace('}', '', $methodLine);
        $parenthesisStart = strpos($methodLine, '(') + 1;
        $parenthesisEnd = strpos($methodLine, ')');
        $methodArgs = substr($methodLine, $parenthesisStart, $parenthesisEnd - $parenthesisStart);
        $methodArgs = explode(',', $methodArgs);

        foreach ($methodArgs as $key => $arg) {
            $methodArgs[$key] = '"'.$arg.'"';
        }

        return $methodArgs;
    }

    private function removeMethodLine(string $content, int $methodAnnotationEnd)
    {
        $newLineIndexes = $this->getNewLineIndexes($content);
        $startIndexToRemove = $newLineIndexes[array_search($methodAnnotationEnd, $newLineIndexes) - 1] + 1;
        $endIndexToRemove = $methodAnnotationEnd + \strlen("\n");
        $lineToRemove = substr($content, $startIndexToRemove, $endIndexToRemove - $startIndexToRemove);

        return str_replace($lineToRemove, '', $content);
    }

    private function addMethodArgInRoute(string $content, array $methodsToMove)
    {
        $routeAnnotationStart = strpos($content, '@Route');
        $openingBraceOfRoute = strpos($content, '(', $routeAnnotationStart);

        if (false !== $conditionIndexStart = strpos($content, 'condition', $openingBraceOfRoute + 1)) {
            $closingIndexEnd = strpos($content, '"', $conditionIndexStart + \strlen('condition') + 2);
            $routeAnnotationEnd = strpos($content, ')', $closingIndexEnd + 1);
        } else {
            $routeAnnotationEnd = strpos($content, ')', $routeAnnotationStart);
        }

        $routeLine = substr($content, $routeAnnotationStart, $routeAnnotationEnd - $routeAnnotationStart);

        $newRouteLine = substr_count($routeLine, "\n") > 1
            ? $this->addMethodArgInMultiLineRoute($routeLine, $methodsToMove)
            : $newRouteLine = $this->addMethodArgInSingleLineRoute($routeLine, $methodsToMove)
        ;

        return str_replace($routeLine, $newRouteLine, $content);
    }

    private function addMethodArgInSingleLineRoute(string $routeLine, array $methodsToMove)
    {
        return str_replace($routeLine, $routeLine.', methods={'.implode(',', $methodsToMove).'}', $routeLine);
    }

    private function addMethodArgInMultiLineRoute(string $routeLine, array $methodsToMove)
    {
        $lastLineBreak = strrpos($routeLine, "\n");
        $replacement = ",\n     *     methods={".implode(',', $methodsToMove).'}';

        return substr_replace($routeLine, $replacement, $lastLineBreak, 0);
    }

    private function getNewLineIndexes(string $content)
    {
        $lastPos = 0;
        $indexes = [];

        while (false !== ($lastPos = strpos($content, "\n", $lastPos))) {
            $indexes[] = $lastPos;
            $lastPos = $lastPos + \strlen("\n");
        }

        return $indexes;
    }
}
