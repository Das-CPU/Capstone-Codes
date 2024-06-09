#include <WiFi.h>
#include <HTTPClient.h>
#include <LiquidCrystal.h>
#include <EEPROM.h>
#define LED_1 33
#define LED_2 25
#define LED_3 26
#define Valve 0
#include "esp_wifi.h"

const char* ssid = "MonaConnect";
//const char* password = "";
const int rs = 19, en = 18, d4 = 4, d5 = 16, d6 = 17, d7 = 5;
LiquidCrystal lcd(rs, en, d4, d5, d6, d7);
const char* serverName = "http://10.22.188.29/insert.php";
int32_t period = 0, current = 0;
int32_t prev = 0;
volatile int32_t freq = 0, shift = 0;
volatile uint8_t Post = 0, Mode = 3; 
volatile uint16_t B_Volt, G_Volt, Amps;

uint16_t Test;
String reading = " ";

struct Flowrate {
  const uint8_t PIN;
  uint32_t pulses;
};

Flowrate flow = {35, 0};

void IRAM_ATTR isr() {
 flow.pulses++;
 prev = current;
 current = micros();
 period = current - prev;
 freq = (1000000/period);
}

void IRAM_ATTR button() {
  Mode++;
  if (Mode == 4)
  {
    Mode = 0;
  }
  digitalWrite(LED_1, HIGH);
}

void LCD_scroll(String Word);

void LCD_scroll(String Word)
{
    lcd.autoscroll();
    lcd.setCursor(16,0);
    String inputString = Word;
    for (int i = 0; i < inputString.length(); i++) {
    char c = inputString.charAt(i);
    lcd.print(c);
    delay(200);
    }
    lcd.noAutoscroll();
}

void setup(){
    //lcd.begin(16, 2);
    pinMode(LED_1, OUTPUT);
    pinMode(LED_2, OUTPUT);
    pinMode(LED_3, OUTPUT);
    pinMode(flow.PIN, INPUT);
    pinMode(Valve, OUTPUT);
    LCD_scroll("Connecting to Wifi");
    
    analogWriteFrequency (50000);
    analogWrite(32, 196);
    

    WiFi.mode(WIFI_STA); //Optional
    WiFi.begin(ssid/*, password*/);
    Serial.println("\nConnecting");
   
    delay(500);
 
    while(WiFi.status() != WL_CONNECTED){
        Serial.print(".");
        LCD_scroll(".");
        delay(300);
    }

    Serial.println("\nConnected to the WiFi network");
    lcd.clear();
    lcd.setCursor(1,0);
    lcd.print("Connected to");
    delay(500);
    lcd.clear();
    lcd.setCursor(1,0);
    lcd.print(ssid);
    Serial.print("Local ESP32 IP: ");
    Serial.println(WiFi.localIP());
    delay(2000);

    attachInterrupt(flow.PIN, isr, FALLING);
    attachInterrupt(34, button, FALLING);
    digitalWrite(Valve, HIGH);

    Serial.begin(9600);
    //Serial1.begin(9600);
 
}

void loop() {

      if (Mode == 3)
      {
  //         if ((current - micros()) > 20000000)
   //             {freq = 0;}
           lcd.clear();
           lcd.setCursor(1,0);
           lcd.print("Q: ");
           lcd.print(freq/7.5);
           lcd.print("L/min");
      }

      if (Mode == 0)
      {
            if (Serial.available() > 0) 
                { 
                  /*for (uint8_t i = 1; i > -1; i--)
                    {*/
                      reading = Serial.read();
                      //shift = reading;
                      //shift = (shift << (8*i));
                      //B_Volt = (B_Volt + shift);
                      //xSerial.println (reading);
                   // }
                }
           lcd.clear();
           lcd.setCursor(1,0);
           lcd.print("Batt: ");
           lcd.print(reading);
           lcd.print("V");
      }

      if (Mode == 1)
      {
           shift = 0;
           for (int i = 1; i > -1; i--){
                if (Serial.available() > 0) {
                  shift = Serial.read();
                  shift = (shift << (8*i));
                  G_Volt = (G_Volt + shift);
                }
           }
           lcd.clear();
           lcd.setCursor(1,0);
           lcd.print("Gen: ");
           lcd.print(G_Volt);
           lcd.print("V");
      }

      if (Mode == 2)
      {
           Serial.write(Mode);
           for (int i = 1; i > -1; i--){
                if (Serial.available() > 0) {
                  shift = Serial.read();
                  shift = (shift << (8*i));
                  Amps = (Amps + shift);
                }
           }
           lcd.clear();
           //lcd.setCursor(1,0);
           //lcd.print("Load: ");
           //lcd.print(Amps);
           //lcd.print("A");
      }

       Post++; 
     
      if(Post ==  30)
      { 
       if (WiFi.status() == WL_CONNECTED) {
           HTTPClient http;
           http.begin(serverName);

           // Specify content type
           http.addHeader("Content-Type", "application/x-www-form-urlencoded");

           // Prepare the data to send
           String sensorValue = String(freq/7); // Example sensor reading
           String httpRequestData = "sensor_value=" + sensorValue;
           Serial.print("Sending data: ");
           Serial.println(httpRequestData);
           
           // Send the HTTP POST request
           int httpResponseCode = http.POST(httpRequestData);
            digitalWrite(LED_1, HIGH);
            delay(40);
            digitalWrite(LED_2, HIGH);
            delay(40);
            digitalWrite(LED_1, LOW);
            delay(40);
            digitalWrite(LED_2, LOW);
            delay(40);
           if (httpResponseCode > 0) {
               String response = http.getString();
               Serial.println(httpResponseCode);
               Serial.println(response);
               digitalWrite(LED_3, LOW);
           } else {
               Serial.print("Error on sending POST: ");
               Serial.println(httpResponseCode);
               digitalWrite(LED_3, HIGH);
               
           }

           http.end();
           Post = 0;
       }
       else {
           Serial.println("Error in WiFi connection");
           lcd.clear();
           LCD_scroll("Error in WiFi connection");
           Post = 0;
       }
      }
       
       delay(1000); // Send data every 30 seconds

}
