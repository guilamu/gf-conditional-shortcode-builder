# **Gravity Forms Conditional Shortcode Builder**

A utility plugin for Gravity Forms that adds a dedicated Form Setting page to visually construct conditional shortcodes, making it easy to display content based on form field values.  
This plugin is fully compatible with the [**Gravity Forms Advanced Conditional Shortcodes**](https://gravitywiz.com/gravity-forms-advanced-conditional-shortcode) plugin (by Gravity Wiz), automatically enabling support for multiple conditions (AND/OR logic) when detected.

![Plugin Screenshot](https://github.com/guilamu/gf-conditional-shortcode-builder/blob/main/screenshot.png)

## **✨ Features**

* **Dedicated UI:** Access the builder directly within the Form Settings menu.  
* **Visual Condition Building:** Easily select fields, operators (is, contains, greater\_than, etc.), and values using dropdowns and inputs.  
* **Real-time Shortcode Generation:** The shortcode is generated instantly as you build your conditions.  
* **Advanced Compatibility:** If the excellent Gravity Wiz [**Gravity Forms Advanced Conditional Shortcodes**](https://gravitywiz.com/gravity-forms-advanced-conditional-shortcode) plugin is active, the builder automatically unlocks:  
  * **Relation Selector:** Choose between **Match ALL (AND)** or **Match ANY (OR)** logic.  
  * **Multiple Conditions:** Add and manage complex, multi-row condition groups.  
* **Simple Copy:** One-click button to copy the finished shortcode to your clipboard.

## **⚙️ Installation**

1. **Download:** Download the latest version of the plugin as a ZIP file.  
2. **Upload:** Go to your WordPress admin dashboard, navigate to **Plugins** \> **Add New** \> **Upload Plugin**.  
3. **Install & Activate:** Choose the ZIP file and click **Install Now**. Once installed, click **Activate Plugin**.

## **🚀 Usage**

### **1\. Access the Builder**

1. In your WordPress dashboard, navigate to **Forms** \> **Forms**.  
2. Hover over the desired form and click **Settings**.  
3. In the left sidebar menu, click on **Conditional Shortcode Builder**.

### **2\. Build Your Condition(s)**

1. **Select a Field:** Use the first dropdown to select the form field you want to evaluate (e.g., "Field ID: 1").  
2. **Select an Operator:** Choose the comparison operator (e.g., is, greater\_than, contains).  
3. **Enter a Value:** Input the static value the field's entry must match (e.g., Yes, 100, user@example.com).

### **3\. Using Advanced Logic (Optional)**

If you have the [**Gravity Forms Advanced Conditional Shortcodes**](https://gravitywiz.com/gravity-forms-advanced-conditional-shortcode) plugin installed, you will see two additional elements:

1. **Relation:** Select Match ALL conditions (AND) or Match ANY condition (OR).  
2. **Add Condition:** Click this button to add another row to create complex, multi-variable logic.

### **4\. Copy and Use**

The generated shortcode will appear in the **Generated Shortcode** textarea.

1. Click the **Copy to Clipboard** button.  
2. Paste the shortcode into any notification, HTML field or confirmation where you want to conditionally display content.

**Example Generated Shortcode:**  
<\!-- Single Condition (Standard GF Shortcode) \--\>  
```
[gravityforms action="conditional" merge_tag="{Field Label:1}" condition="is" value="red"]
   This content shows if Field 1 is 'red'.  
[/gravityforms]
``` 
\<\!-- Multiple Conditions (Advanced Shortcode) \--\>  
```
[gravityforms action="conditional" relation="and" merge_tag="{Name:1}" condition="isnot" value="" merge_tag_2="{Age:2}" condition_2="greater_than" value_2="18"]  
   This content shows if the Name field is NOT empty AND the Age is greater than 18.  
[/gravityforms]
```
### License

This project is licensed under the GNU AGPL.
