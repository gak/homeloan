#!/usr/bin/env python

class Loan:

    def __init__(self, principal, annual_interest_rate, years):
        self.principal = principal
        self.annual_interest_rate = annual_interest_rate
        self.years = years

    @property
    def monthly_interest_rate(self):
        return self.annual_interest_rate / 12.

    @property
    def months(self):
        return self.years * 12.

    @property
    def monthly_factor(self):
        factor = 0.
        base_rate = 1. + self.monthly_interest_rate
        denominator = base_rate
        for a in xrange(self.months):
            factor += (1. / denominator)
            denominator *= base_rate
        return factor

    @property
    def monthly_payment(self):
        return self.principal / self.monthly_factor

    def loop(self):

        current_month = 1
        current_year = 1
        principal = self.principal

        # while current_month <= self.months:
        while principal > 0:

            interest_paid = principal * self.monthly_interest_rate
            principal_paid = self.monthly_payment - interest_paid
#            if current_month == 12:
#                principal_paid += 5000
            remaining_balance = principal - principal_paid

            principal = remaining_balance

            print '%2i %2i' % (int(current_month / 12), current_month % 12),
            print 'interest paid: %8.2f' % interest_paid,
            print 'principal paid: %8.2f' % principal_paid,
            print 'remaining balance: %9.2f' % remaining_balance

            current_month += 1

    def print_info(self):
        print 'monthly interest rate:', self.monthly_interest_rate
        print 'repayments:', self.months
        print 'monthly repayment:', self.monthly_payment

if __name__ == '__main__':
    loan = Loan(450000., 0.08, 20)
    loan.loop()
    loan.print_info()

