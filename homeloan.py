#!/usr/bin/env python

import math

FREQ_YEARLY = 0
FREQ_MONTHLY = 1
FREQ_FORTNIGHTLY = 2
FREQ_WEEKLY = 3

class Loan:

    def __init__(self, principal, annual_interest_rate, years, frequency):
        self.principal = principal
        self.annual_interest_rate = annual_interest_rate
        self.years = years
        self.frequency = frequency

        self.calculate_frequency()
        self.factor = self.calculate_factor()

    def calculate_frequency(self):
        if self.frequency == FREQ_YEARLY:
            div = 1
        if self.frequency == FREQ_MONTHLY:
            div = 12
        if self.frequency == FREQ_FORTNIGHTLY:
            div = 365.24 / 14.
        if self.frequency == FREQ_WEEKLY:
            div = 365.24 / 7.

        self.interest_rate = self.annual_interest_rate / div
        self.periods = self.years * div
        self.periods = math.ceil(self.periods)

    @property
    def payment(self):
        return self.principal / self.factor

    def calculate_factor(self):
        factor = 0.
        base_rate = 1. + self.interest_rate
        denominator = base_rate
        for a in xrange(self.periods):
            factor += (1. / denominator)
            denominator *= base_rate
        return factor

    def loop(self):

        current_period = 1
        principal = self.principal

        while principal > 0:

            interest_paid = principal * self.interest_rate
            principal_paid = self.payment - interest_paid
            remaining_balance = principal - principal_paid

            principal = remaining_balance

            print '%2i' % current_period,
            print 'interest paid: %8.2f' % interest_paid,
            print 'principal paid: %8.2f' % principal_paid,
            print 'remaining balance: %9.2f' % remaining_balance

            current_period += 1

    def print_info(self):
        print 'repayments: %7i' % self.periods,
        print 'period repayment: %10.2f' % self.payment,
        print

if __name__ == '__main__':
    loan = Loan(450000., 0.08, 20, FREQ_YEARLY)
    loan.print_info()
    loan = Loan(450000., 0.08, 20, FREQ_MONTHLY)
    loan.print_info()
    loan = Loan(450000., 0.08, 20, FREQ_WEEKLY)
    loan.print_info()

