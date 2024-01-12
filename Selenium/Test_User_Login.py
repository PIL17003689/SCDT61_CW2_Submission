from Selenium_Imports import *

# Insert text into text boxes
def Text_Input(xpath, text):
    textbox = driver.find_element(By.XPATH, xpath)
    textbox.clear()
    textbox.send_keys(text)

# Insert text into text boxes based on 
def Placeholder_Input(placeholder, text):
    Text_Input("//input[@placeholder='"+placeholder+"']",text)

# Click button based on Text
def Click_Input_by_Text(object, text):
    button = driver.find_element(By.XPATH, "//"+object+"[(text()='"+text+"')]")
    button.click()


options = Options()
driver = webdriver.Chrome()
driver.get("http://localhost/Kieran's%20Files/SD&QA/CW2/SCDT61_CW2_Submission/login.php")

Placeholder_Input("Email", "test1@email.com")
Placeholder_Input("Password", "Test123!") 
time.sleep(2)
Click_Input_by_Text("button", "Login")

time.sleep(3)
driver.quit()