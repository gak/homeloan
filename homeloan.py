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

    def loop(self, output=False):

        current_period = 1
        principal = self.principal
        total_interest_paid = 0
        current_date = date.today()

        while True:

            interest_paid = principal * self.interest_rate
            principal_paid = self.payment - interest_paid
            remaining_balance = principal - principal_paid

            if remaining_balance <= 0:
                break

            total_interest_paid += interest_paid

            principal = remaining_balance

            total_paid = interest_paid + principal_paid

            if output:
                print '%2i' % current_period,
                print '%10s' % current_date,
                print 'interest paid: %8.2f' % interest_paid,
                print 'principal paid: %8.2f' % principal_paid,
                print 'paid: %8.2f' % total_paid,
                print 'remaining balance: %9.2f' % remaining_balance

            current_period += 1
            days = Loan.interval_for_frequency(self.frequency)
            current_date += timedelta(days=days)

        return total_interest_paid

    def print_info(self):
        total_interest_paid = self.loop()
        print 'repayments: %7i' % self.periods,
        print 'period repayment: %10.2f' % self.payment,
        print 'total interest_paid: %10.2f' % total_interest_paid,
        print

if __name__ == '__main__':
    loan = Loan(420000 * 0.8, 0.09, 30, FREQ_MONTHLY)
    loan.loop(True)
    loan.print_info()

