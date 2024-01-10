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
options.add_argument("--window-size=1920,1080")
driver = webdriver.Chrome(options=options)
driver.get("http://localhost/Kieran's%20Files/SD&QA/CW2/SCDT61_CW2_Submission/")

time.sleep(2)
Click_Input_by_Text("a", "Login")
time.sleep(2)
Click_Input_by_Text("a", "Online Shop")


time.sleep(5)