import { StyleSheet, Text, View, Image, TouchableOpacity } from 'react-native'
import React, { useState } from 'react'
import { AntDesign } from '@expo/vector-icons';
import { FontAwesome } from '@expo/vector-icons';

const Solicitacaoagen = (
    {

        profile_image,
        username,
       
    }
) => {



    return (
        <View style={styles.container}>
           
            <View style={styles.c1}>
                <Image source={{ uri: profile_image }} style={styles.profilepic} />
                <Text style={styles.username}>{username}</Text>
                
            </View>

            <View style={{width: '95%', borderColor: '#ec230d', borderWidth: 0.5, borderRadius: 10,
        height: 100, alignSelf: 'center', backgroundColor: '#ec230d', elevation: 10, 
        flexDirection: 'row'}}>

                <View>
                <Text style={{marginStart: 20, marginTop: 10, fontSize: 20,
                color: 'white', fontWeight: 'bold'}}>Horário:</Text>
                <Text style={{marginStart: 20, marginTop: 5, fontSize: 30,
                color: 'white', fontWeight: 'bold'}}>10:00 AM</Text>
                </View>
                <View style={{width: 100, height: 40, backgroundColor: 'gray',
                color: 'white', borderRadius: 10, alignItems: 'center', justifyContent: 'center',
                alignSelf: 'center', marginStart: 50, borderColor: 'white', borderWidth: 1 }}>

                    <Text style={{color: 'white', fontWeight: 'bold', fontSize: 13}}>Pendente</Text>
                </View>

            </View>
                    <Text style={{marginStart: 20, marginTop: 12, fontSize: 15,
                    fontWeight: 'bold'}}>Confirme a solicitação</Text>

                    <View style={{flexDirection: 'row'}}>

                            <TouchableOpacity style={{marginTop: 7,marginStart: 30, width: 40, height: 40,
                            backgroundColor: '#ec230d', alignItems: 'center', justifyContent: 'center',
                            borderRadius: 300, elevation: 10}}>
                            <AntDesign name="check" size={15} color="white" style={styles.iconliked} />
                            </TouchableOpacity>
        
                    <TouchableOpacity style={{width: 40, height: 40, backgroundColor: 'white', elevation: 10, 
                justifyContent: 'center', alignItems: 'center', borderRadius: 150, marginStart: 210,
                marginTop: 7}}>
                    <FontAwesome name="close" size={18} color="red" />
                    </TouchableOpacity>

                    </View>
          


           
        </View>

    )
}

export default Solicitacaoagen

const styles = StyleSheet.create({
    container: {
        backgroundColor: 'white',
        width: '90%',
        height: 260,
        borderRadius: 20,
        marginVertical: 10,
        overflow: 'hidden',
        borderColor: 'white',
        borderWidth: 1,
        alignSelf: 'center',
        elevation: 10
    },
    c1: {
        width: '100%',
        flexDirection: 'row',
        alignItems: 'center',
        padding: 10,
        backgroundColor: 'white',
        marginTop: -3
    },
    profilepic: {
        width: 40,
        height: 40,
        borderRadius: 30,
        borderColor: 'white',
        borderWidth: 1,
        marginTop: 5
    },
    username: {
        color: 'black',
        marginLeft: 10,
        fontSize: 17,
        fontWeight: 'bold',
    },
    image: {
        width: '95%',
        aspectRatio: 1,
        alignSelf: 'center',
        borderRadius: 20,
        marginTop: 10

    },
    s2: {
        width: '100%',
        flexDirection: 'row',
        backgroundColor: 'white',
        padding: 10,
        alignItems: 'center',
        marginTop: 100,
        height: 10,
    },
    s21: {
        // width: '100%',
        flexDirection: 'row',
        alignItems: 'center',
    },
    notliked: {
        color: 'grey',
        marginLeft: 5,
        fontSize: 25,
    },
    liked: {
        color: '#DC143C',
        marginLeft: 7,
        fontSize: 15,
        marginTop: -180
    },
    iconliked: {
        color: 'white',
        fontSize: 20
    },
    s22: {
        width: 40, height: 40,
        elevation: 10, 
        marginTop: -150,
        marginStart: -55,
        backgroundColor: 'white',
        alignItems: 'center',
        justifyContent: 'center',
        borderRadius: 150
        
    },
    s3: {
        width: '100%',
        backgroundColor: 'white',
        padding: 10,
    },
    commentuser: {
        color: 'black',
        fontWeight: 'bold',
        fontSize: 17,

    },
    commenttext: {
        color: 'black',
        fontSize: 17,
        marginLeft: 5,
    },
    s31: {
        flexDirection: 'row',
        alignItems: 'center',
        marginVertical: 3,
    }

})