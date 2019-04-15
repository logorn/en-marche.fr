<?php

namespace AppBundle\Validator;

use AppBundle\Entity\Adherent;
use AppBundle\Repository\AdherentRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class CanBeCoReferentValidator extends ConstraintValidator
{
    private $adherentRepository;

    public function __construct(AdherentRepository $adherentRepository)
    {
        $this->adherentRepository = $adherentRepository;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof CanBeCoReferent) {
            throw new UnexpectedTypeException($constraint, CanBeCoReferent::class);
        }

        if (null === $value) {
            return;
        }

        if (!\is_bool($value)) {
            throw new UnexpectedTypeException($value, 'boolean');
        }

        $alreadyCoReferent = $this->adherentRepository->find($this->context->getObject()->get);

        if ($alreadyCoReferent) {
            $this->context->buildViolation($constraint->message)
                ->addViolation()
            ;
        }
    }
}
