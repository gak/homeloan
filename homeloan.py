#!/usr/bin/env python

import math
from datetime import date, timedelta

FREQ_YEARLY = 0
FREQ_MONTHLY = 1
FREQ_FORTNIGHTLY = 2
FREQ_WEEKLY = 3

DAYS_IN_YEAR = 365.24

class Loan:

    def __init__(self, principal, annual_interest_rate, years, frequency):
        self.principal = principal
        self.annual_interest_rate = annual_interest_rate
        self.years = years
        self.frequency = frequency
        self.calculate_frequency()
        self.factor = self.calculate_factor()

    @staticmethod
    def interval_for_frequency(freq):
        if FREQ_YEARLY:
            return DAYS_IN_YEAR
        if FREQ_MONTHLY:
            return DAYS_IN_YEAR / 12
        if FREQ_FORTNIGHTLY:
            return 14
        if FREQ_WEEKLY:
            return 7

    def calculate_frequency(self):
        div = DAYS_IN_YEAR / self.interval_for_frequency(self.frequency)
        self.interest_rate = self.annual_interest_rate / div
        self.periods = self.years * div
        self.periods = int(math.ceil(self.periods))

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

    def get_extra_principal_paid(self, days):
        return 0

    def loop(self, output=False):

        current_period = 1
        principal = self.principal
        total_interest_paid = 0
        current_date = date.today()
        days_in_period = Loan.interval_for_frequency(self.frequency)
        days = 0

        while True:

            current_period += 1
            delta = timedelta(days=days_in_period)
            current_date += delta
            days += days_in_period

            interest_paid = principal * self.interest_rate
            principal_paid = self.payment - interest_paid

            # extra repayments?
            extra_principal_paid = self.get_extra_principal_paid(days)
            principal_paid += extra_principal_paid

            remaining_balance = principal - principal_paid

            if remaining_balance <= 0:
                break

            total_interest_paid += interest_paid

            principal = remaining_balance

            total_paid = interest_paid + principal_paid

            if output:
                print '%2i' % current_period,
                print '%10s' % current_date,
                print 'Year: %4.1f' % (days / DAYS_IN_YEAR),
                print 'interest paid: %8.2f' % interest_paid,
                print 'principal paid: %8.2f (+%5.2f)' % (principal_paid,
                        extra_principal_paid),
                print 'paid: %8.2f' % total_paid,
                print 'remaining balance: %9.2f' % remaining_balance


        self.real_periods = current_period

        return total_interest_paid

    def print_info(self):
        total_interest_paid = self.loop()
        print 'Loan amount:          %7i' % self.principal,
        print 'Interest rate         %2.2f%%' % (self.annual_interest_rate *
                100),
        print 'predicted repayments: %3i' % self.periods,
        print 'real repayments:      %3i' % self.real_periods,
        print 'period repayment:     %7.2f' % self.payment,
        print 'total interest_paid:  %10.2f' % total_interest_paid
        print

if __name__ == '__main__':
    loan = Loan(465000 * 0.8, 0.07, 30, FREQ_MONTHLY)
    loan.loop(False)
    loan.print_info()

