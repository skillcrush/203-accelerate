## Description

This document describes the architecture of the Ninja Forms database layer.

## Current Structure (1.1)

This section contains the current structure of the Ninja Forms data layer and is updated each time that structure changes. For legacy data, please see the individual version notes.

### Forms

_**nf3_forms**_ (Table of individual Forms)
* id (The unique ID of the Form)
  * int(11)
  * NOT NULL
  * AUTO_INCREMENT
  * Primary Key
* title (The displayable title of the Form)
  * longtext
  * COLLATE DATABASE_DEFAULT
* key (The administrative key of the Form)
  * longtext
  * COLLATE DATABASE_DEFAULT
* created_at (The date/time the Form was created)
  * timestamp
  * NOT NULL
  * DEFAULT CURRENT_TIMESTAMP
  * ON UPDATE CURRENT_TIMESTAMP
* updated_at (The date/time the Form was last updated)
  * datetime
* views (The number of times the Form has been viewed)
  * int(11)
* subs (The Form's number of lifetime Submissions)
  * int(11)
* form_title (The displayable title of the Form)
  * longtext
  * COLLATE utf8mb4_general_ci (fallback to utf8_general_ci)
* default_label_pos (The default label position of fields on the Form)
  * varchar(15)
* show_title (Whether or not to show the Form title)
  * bit(1)
* clear_complete (Whether or not to clear the Form after submission)
  * bit(1)
* hide_complete (Whether or not to hide the Form after submission)
  * bit(1)
* logged_in (Whether or not the user must be logged in to view the Form)
  * bit(1)
* seq_num (The number of the next submission to the Form)
  * int(11)


_**nf3_form_meta**_ (Table of Settings assoicated with each Form)
* id (The unique ID of the Setting)
  * int(11)
  * NOT NULL
  * AUTO_INCREMENT
  * Primary Key
* parent_id (The Form ID this Setting is associated with)
  * int(11)
  * NOT NULL
  * Foreign Key ON *nf3_forms* id
* key (The administrative key of the Setting)
  * longtext
  * COLLATE DATABASE_DEFAULT
  * NOT NULL
* value (The value of the Setting)
  * longtext
  * COLLATE DATABASE_DEFAULT
* meta_key (The administrative key of the Setting)
  * longtext
  * COLLATE utf8mb4_general_ci (fallback to utf8_general_ci)
* meta_value (The value of the Setting)
  * longtext
  * COLLATE utf8mb4_general_ci (fallback to utf8_general_ci)

### Fields

_**nf3_fields**_ (Table of individual Fields)
* id (The unique ID of the Field)
  * int(11)
  * NOT NULL
  * AUTO_INCREMENT
  * Primary Key
* label (The displayable label of the Field)
  * longtext
  * COLLATE DATABASE_DEFAULT
* key (The administrative key of the Field)
  * longtext
  * COLLATE DATABASE_DEFAULT
* type (The type of Field this record represents)
  * longtext
  * COLLATE DATABASE_DEFAULT
* parent_id (The Form ID this Field is associated with)
  * int(11)
  * NOT NULL
  * Foreign Key ON *nf3_forms* id
* created_at (The date/time the Field was created)
  * timestamp
  * NOT NULL
  * DEFAULT CURRENT_TIMESTAMP
  * ON UPDATE CURRENT_TIMESTAMP
* updated_at (The date/time the Field was last updated)
  * datetime


_**nf3_field_meta**_ (Table of Settings associated with each Field)
* id (The unique ID of the Setting)
  * int(11)
  * NOT NULL
  * AUTO_INCREMENT
  * Primary Key
* parent_id (The Field ID this Setting is associated with)
  * int(11)
  * NOT NULL
  * Foreign Key ON *nf3_fields* id
* key (The administrative key of the Setting)
  * longtext
  * COLLATE DATABASE_DEFAULT
  * NOT NULL
* value (The value of the Setting)
  * longtext
  * COLLATE DATABASE_DEFAULT
  
### Actions

_**nf3_actions**_ (Table of individual Actions)
* id (The unique ID of the Action)
  * int(11)
  * NOT NULL
  * AUTO_INCREMENT
  * Primrary Key
* title (The displayable title of the Action)
  * longtext
  * COLLATE DATABASE_DEFAULT
* key (The administrative key of the Action)
  * longtext
  * COLLATE DATABASE_DEFAULT
* type (The type of Action this record represents)
  * longtext
  * COLLATE DATABASE_DEFAULT
* active (Whether or not the Action is active)
  * tinyint(1)
  * DEFAULT 1
* parent_id (The Form ID this Action is associated with)
  * int(11)
  * NOT NULL
  * Foreign Key ON *nf3_forms* id
* created_at (The date/time the Action was created)
  * timestamp
  * NOT NULL
  * DEFAULT CURRENT_TIMESTAMP
  * ON UPDATE CURRENT_TIMESTAMP
* updated_at (The date/time the Action was last updated)
  * datetime


_**nf3_action_meta**_ (Table of Settings associated with each Action)
* id (The unique ID of the Setting)
  * int(11)
  * NOT NULL
  * AUTO_INCREMENT
  * Primary KEY
* parent_id (The Action ID this Setting is associated with)
  * int(11)
  * NOT_NULL
  * Foreign Key ON *nf3_actions* id
* key (The administrative key of the Setting)
  * longtext
  * COLLATE DATABASE_DEFAULT
  * NOT NULL
* value (The value of the Setting)
  * longtext
  * COLLATE DATABASE_DEFAULT

### Objects

_**nf3_objects**_ (Table of non-structured Objects)
* id (The unique ID of the Object)
  * int(11)
  * NOT NULL
  * AUTO_INCREMENT
  * Primrary Key
* type (The type of Object this record represents)
  * longtext
  * COLLATE DATABASE_DEFAULT
* title (The displayable title of the Object)
  * longtext
  * COLLATE DATABASE_DEFAULT
* created_at (The date/time the Object was created)
  * timestamp
  * NOT NULL
  * DEFAULT CURRENT_TIMESTAMP
  * ON UPDATE CURRENT_TIMESTAMP
* updated_at (The date/time the Object was last updated)
  * datetime


_**nf3_object_meta**_ (Table of Settings associated with each Object)
* id (The unique ID of the Setting)
  * int(11)
  * NOT NULL
  * AUTO_INCREMENT
  * Primary KEY
* parent_id (The Object ID this Setting is associated with)
  * int(11)
  * NOT_NULL
  * Foreign Key ON *nf3_objects* id
* key (The administrative key of the Setting)
  * longtext
  * COLLATE DATABASE_DEFAULT
  * NOT NULL
* value (The value of the Setting)
  * longtext
  * COLLATE DATABASE_DEFAULT


_**nf3_relationships**_ (Table of Relationships between Objects)
* id (The unique ID of the Relationship)
  * int(11)
  * NOT NULL
  * AUTO_INCREMENT
  * Primary KEY
* child_id (The child Object ID this record is associated with)
  * int(11)
  * NOT_NULL
  * Foreign Key ON *nf3_objects* id
* child_type (The type of Object represented by child_id)
  * longtext
  * COLLATE DATABASE_DEFAULT
  * NOT NULL
* parent_id (The parent Object ID this record is associated with)
  * int(11)
  * NOT_NULL
  * Foreign Key ON *nf3_objects* id
* parent_type (The type of Object represented by parent_id)
  * longtext
  * COLLATE DATABASE_DEFAULT
  * NOT NULL
* created_at (The date/time the Relationship was created)
  * timestamp
  * NOT NULL
  * DEFAULT CURRENT_TIMESTAMP
  * ON UPDATE CURRENT_TIMESTAMP
* updated_at (The date/time the Relationship was last updated)
  * datetime


_**options**_ (The default WordPress options table)
* option_name = 'nf_form_%' WHERE % = *nf3_forms* id
* option_value = Serialized JSON Object (The Ninja Forms Cache)

### Submissions

_**posts**_ (The default WordPress posts table)
* id (The unique ID of the Post)
  * bigint(20)
  * NOT NULL
  * AUTO_INCREMENT
  * Primary Key
* post_type = 'nf_sub'


_**postmeta**_ (The default WordPress postmeta table)
* meta_id (The unique ID of the Metadata)
  * bigint(20)
  * NOT NULL
  * AUTO_INCREMENT
  * Primary Key
* post_id (The Post ID this Metadata is associated with)
  * bigint(20)
  * NOT NULL
  * DEFAULT 0
  * Foreign Key ON *posts* id
* meta_key (The identifiable key by which this record is referenced)
  * varchar(255)
  * COLLATE DATABASE_DEFAULT
* meta_value (The value of this record)
  * longtext
  * COLLATE DATABASE_DEFAULT

### Upgrades

_**nf3_upgrades**_ (Table of Forms as they exist in the current structure and the stage of upgrade currently applied to each Form)
* id (The unique ID of the Form)
  * int(11)
  * NOT NULL
  * Primary Key
* cache (The Ninja Forms cache as it was retrieved from the *options* table)
  * longtext
  * COLLATE utf8mb4_general_ci (fallback to utf8_general_ci)
* stage (The current upgrade stage of the Form)
  * int(11)
  * NOT NULL
  * DEFAULT 0
  
### Chunks

_**nf3_chunks**_ (Table of Chunks created on publish to be reconstructed into a Ninja Forms cache)
* id (The unique ID of the Chunk)
  * int(11)
  * NOT NULL
  * AUTO_INCREMENT
  * Primary Key
* name (The name of the Chunk)
  * varchar(200)
  * COLLATE utf8mb4_general_ci (fallback to utf8_general_ci)
* value (The value of the Chunk)
  * longtext
  * COLLATE utf8mb4_general_ci (fallback to utf8_general_ci)

### Chunks

_**nf3_chunks**_ (Table of Chunks created on publish to be reconstructed into a Ninja Forms cache)
* id (The unique ID of the Chunk)
  * int(11)
  * NOT NULL
  * AUTO_INCREMENT
  * Primary Key
* name (The name of the Chunk)
  * varchar(200)
  * COLLATE utf8mb4_general_ci (fallback to utf8_general_ci)
* value (The value of the Chunk)
  * longtext
  * COLLATE utf8mb4_general_ci (fallback to utf8_general_ci)

## Version 1.0

Defined initial structure for Ninja Forms data layer.

### Forms

_**nf3_forms**_ (Table of individual Forms)
* id (The unique ID of the Form)
  * int(11)
  * NOT NULL
  * AUTO_INCREMENT
  * Primary Key
* title (The displayable title of the Form)
  * longtext
  * COLLATE DATABASE_DEFAULT
* key (The administrative key of the Form)
  * longtext
  * COLLATE DATABASE_DEFAULT
* created_at (The date/time the Form was created)
  * timestamp
  * NOT NULL
  * DEFAULT CURRENT_TIMESTAMP
  * ON UPDATE CURRENT_TIMESTAMP
* updated_at (The date/time the Form was last updated)
  * datetime
* views (The number of times the Form has been viewed)
  * int(11)
* subs (The Form's number of lifetime Submissions)
  * int(11)


_**nf3_form_meta**_ (Table of Settings assoicated with each Form)
* id (The unique ID of the Setting)
  * int(11)
  * NOT NULL
  * AUTO_INCREMENT
  * Primary Key
* parent_id (The Form ID this Setting is associated with)
  * int(11)
  * NOT NULL
  * Foreign Key ON *nf3_forms* id
* key (The administrative key of the Setting)
  * longtext
  * COLLATE DATABASE_DEFAULT
  * NOT NULL
* value (The value of the Setting)
  * longtext
  * COLLATE DATABASE_DEFAULT

### Fields

_**nf3_fields**_ (Table of individual Fields)
* id (The unique ID of the Field)
  * int(11)
  * NOT NULL
  * AUTO_INCREMENT
  * Primary Key
* label (The displayable label of the Field)
  * longtext
  * COLLATE DATABASE_DEFAULT
* key (The administrative key of the Field)
  * longtext
  * COLLATE DATABASE_DEFAULT
* type (The type of Field this record represents)
  * longtext
  * COLLATE DATABASE_DEFAULT
* parent_id (The Form ID this Field is associated with)
  * int(11)
  * NOT NULL
  * Foreign Key ON *nf3_forms* id
* created_at (The date/time the Field was created)
  * timestamp
  * NOT NULL
  * DEFAULT CURRENT_TIMESTAMP
  * ON UPDATE CURRENT_TIMESTAMP
* updated_at (The date/time the Field was last updated)
  * datetime


_**nf3_field_meta**_ (Table of Settings associated with each Field)
* id (The unique ID of the Setting)
  * int(11)
  * NOT NULL
  * AUTO_INCREMENT
  * Primary Key
* parent_id (The Field ID this Setting is associated with)
  * int(11)
  * NOT NULL
  * Foreign Key ON *nf3_fields* id
* key (The administrative key of the Setting)
  * longtext
  * COLLATE DATABASE_DEFAULT
  * NOT NULL
* value (The value of the Setting)
  * longtext
  * COLLATE DATABASE_DEFAULT
  
### Actions

_**nf3_actions**_ (Table of individual Actions)
* id (The unique ID of the Action)
  * int(11)
  * NOT NULL
  * AUTO_INCREMENT
  * Primrary Key
* title (The displayable title of the Action)
  * longtext
  * COLLATE DATABASE_DEFAULT
* key (The administrative key of the Action)
  * longtext
  * COLLATE DATABASE_DEFAULT
* type (The type of Action this record represents)
  * longtext
  * COLLATE DATABASE_DEFAULT
* active (Whether or not the Action is active)
  * tinyint(1)
  * DEFAULT 1
* parent_id (The Form ID this Action is associated with)
  * int(11)
  * NOT NULL
  * Foreign Key ON *nf3_forms* id
* created_at (The date/time the Action was created)
  * timestamp
  * NOT NULL
  * DEFAULT CURRENT_TIMESTAMP
  * ON UPDATE CURRENT_TIMESTAMP
* updated_at (The date/time the Action was last updated)
  * datetime


_**nf3_action_meta**_ (Table of Settings associated with each Action)
* id (The unique ID of the Setting)
  * int(11)
  * NOT NULL
  * AUTO_INCREMENT
  * Primary KEY
* parent_id (The Action ID this Setting is associated with)
  * int(11)
  * NOT_NULL
  * Foreign Key ON *nf3_actions* id
* key (The administrative key of the Setting)
  * longtext
  * COLLATE DATABASE_DEFAULT
  * NOT NULL
* value (The value of the Setting)
  * longtext
  * COLLATE DATABASE_DEFAULT

### Objects

_**nf3_objects**_ (Table of non-structured Objects)
* id (The unique ID of the Object)
  * int(11)
  * NOT NULL
  * AUTO_INCREMENT
  * Primrary Key
* type (The type of Object this record represents)
  * longtext
  * COLLATE DATABASE_DEFAULT
* title (The displayable title of the Object)
  * longtext
  * COLLATE DATABASE_DEFAULT
* created_at (The date/time the Object was created)
  * timestamp
  * NOT NULL
  * DEFAULT CURRENT_TIMESTAMP
  * ON UPDATE CURRENT_TIMESTAMP
* updated_at (The date/time the Object was last updated)
  * datetime


_**nf3_object_meta**_ (Table of Settings associated with each Object)
* id (The unique ID of the Setting)
  * int(11)
  * NOT NULL
  * AUTO_INCREMENT
  * Primary KEY
* parent_id (The Object ID this Setting is associated with)
  * int(11)
  * NOT_NULL
  * Foreign Key ON *nf3_objects* id
* key (The administrative key of the Setting)
  * longtext
  * COLLATE DATABASE_DEFAULT
  * NOT NULL
* value (The value of the Setting)
  * longtext
  * COLLATE DATABASE_DEFAULT


_**nf3_relationships**_ (Table of Relationships between Objects)
* id (The unique ID of the Relationship)
  * int(11)
  * NOT NULL
  * AUTO_INCREMENT
  * Primary KEY
* child_id (The child Object ID this record is associated with)
  * int(11)
  * NOT_NULL
  * Foreign Key ON *nf3_objects* id
* child_type (The type of Object represented by child_id)
  * longtext
  * COLLATE DATABASE_DEFAULT
  * NOT NULL
* parent_id (The parent Object ID this record is associated with)
  * int(11)
  * NOT_NULL
  * Foreign Key ON *nf3_objects* id
* parent_type (The type of Object represented by parent_id)
  * longtext
  * COLLATE DATABASE_DEFAULT
  * NOT NULL
* created_at (The date/time the Relationship was created)
  * timestamp
  * NOT NULL
  * DEFAULT CURRENT_TIMESTAMP
  * ON UPDATE CURRENT_TIMESTAMP
* updated_at (The date/time the Relationship was last updated)
  * datetime


_**options**_ (The default WordPress options table)
* option_name = 'nf_form_%' WHERE % = *nf3_forms* id
* option_value = Serialized JSON Object (The Ninja Forms Cache)

### Submissions

_**posts**_ (The default WordPress posts table)
* id (The unique ID of the Post)
  * bigint(20)
  * NOT NULL
  * AUTO_INCREMENT
  * Primary Key
* post_type = 'nf_sub'


_**postmeta**_ (The default WordPress postmeta table)
* meta_id (The unique ID of the Metadata)
  * bigint(20)
  * NOT NULL
  * AUTO_INCREMENT
  * Primary Key
* post_id (The Post ID this Metadata is associated with)
  * bigint(20)
  * NOT NULL
  * DEFAULT 0
  * Foreign Key ON *posts* id
* meta_key (The identifiable key by which this record is referenced)
  * varchar(255)
  * COLLATE DATABASE_DEFAULT
* meta_value (The value of this record)
  * longtext
  * COLLATE DATABASE_DEFAULT

## Version 1.1

Defined tracker table for data structure updates.

### Upgrades

_**nf3_upgrades**_ (Table of Forms as they exist in the current structure and the stage of upgrade currently applied to each Form)
* id (The unique ID of the Form)
  * int(11)
  * NOT NULL
  * Primary Key
* cache (The Ninja Forms cache as it was retrieved from the *options* table)
  * longtext
  * COLLATE utf8mb4_general_ci (fallback to utf8_general_ci)
* stage (The current upgrade stage of the Form)
  * int(11)
  * NOT NULL
  * DEFAULT 0

Defined chunks table for ensuring collate of chunked publish.

### Chunks

_**nf3_chunks**_ (Table of Chunks created on publish to be reconstructed into a Ninja Forms cache)
* id (The unique ID of the Chunk)
  * int(11)
  * NOT NULL
  * AUTO_INCREMENT
  * Primary Key
* name (The name of the Chunk)
  * varchar(200)
  * COLLATE utf8mb4_general_ci (fallback to utf8_general_ci)
* value (The value of the Chunk)
  * longtext
  * COLLATE utf8mb4_general_ci (fallback to utf8_general_ci)

Updated forms tables

### Forms

_**nf3_forms**_ (Table of individual Forms)
ADDED:
* form_title (The displayable title of the Form)
  * longtext
  * COLLATE utf8mb4_general_ci (fallback to utf8_general_ci)
* default_label_pos (The default label position of fields on the Form)
  * varchar(15)
* show_title (Whether or not to show the Form title)
  * bit(1)
* clear_complete (Whether or not to clear the Form after submission)
  * bit(1)
* hide_complete (Whether or not to hide the Form after submission)
  * bit(1)
* logged_in (Whether or not the user must be logged in to view the Form)
  * bit(1)
* seq_num (The number of the next submission to the Form)
  * int(11)

_**nf3_form_meta**_ (Table of Settings assoicated with each Form)
ADDED:
* meta_key (The administrative key of the Setting)
  * longtext
  * COLLATE utf8mb4_general_ci (fallback to utf8_general_ci)
* meta_value (The value of the Setting)
  * longtext
  * COLLATE utf8mb4_general_ci (fallback to utf8_general_ci)
