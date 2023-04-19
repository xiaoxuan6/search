import argparse
import time

from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.support.ui import WebDriverWait
from webdriver_manager.chrome import ChromeDriverManager


def exec(email: str, password: str):
    chrome_options = webdriver.ChromeOptions()
    chrome_options.add_argument('--no-sandbox')
    chrome_options.add_argument('--headless')
    chrome_options.add_experimental_option('excludeSwitches', ['enable-automation'])
    chrome_options.add_argument('--disable-extensions')

    driver = webdriver.Chrome(ChromeDriverManager().install(), options=chrome_options)
    driver.get('http://localhost:9200/#/login?redirect=%2Fstatus%2Fonline')
    emailElement = WebDriverWait(driver, 5).until(
        EC.presence_of_element_located(
            (By.XPATH, '//*[@id="app"]/div/form/div[2]/div/div/input')
        )
    )
    passwordElement = WebDriverWait(driver, 5).until(
        EC.presence_of_element_located(
            (By.XPATH, '//*[@id="app"]/div/form/div[3]/div/div/input')
        )
    )

    emailElement.send_keys(email)
    passwordElement.clear()
    passwordElement.send_keys(password)
    driver.find_element_by_xpath('//*[@id="app"]/div/form/button').click()

    time.sleep(1)
    driver.get('http://localhost:9200/#/status/online')
    time.sleep(1)

    print(driver.find_element_by_xpath(
        '//*[@id="app"]/div/div[2]/section/div/div/div[3]/table/tbody/tr[1]/td[3]/div').text)
    driver.quit()


if __name__ == '__main__':
    arg = argparse.ArgumentParser()
    arg.add_argument('--email', type=str)
    arg.add_argument('--password', type=str)
    args = arg.parse_args()

    exec(args.email, args.password)
