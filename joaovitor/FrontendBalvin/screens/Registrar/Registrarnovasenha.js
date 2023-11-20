import { StyleSheet, Text, View, Image, TextInput, TouchableOpacity, StatusBar } from 'react-native'
import React, { useState } from 'react'
import { MaterialIcons } from '@expo/vector-icons';

const Registrarnovasenha = ({ navigation, route }) => {
    const { email, nome } = route.params;
    const [senha, setSenha] = useState('')
    const [confirmarsenha, setConfirmsenha] = useState('')
    const [loading, setLoading] = useState(false)


    const handlePassword = () => {

        
        if (senha == '' || setSenha == '') {
            alert('Por favor entre com um senha')
        } else if (senha != confirmarsenha) {
            alert('Senha não se coincidem')
        }
        else {
            setLoading(true)
            fetch('http://192.168.0.54:3000/registrar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ email: email, nome: nome, senha: senha })
            })
                .then(res => res.json()).then(
                    data => {
                        if (data.message === "Usuário registrado com sucesso") {
                            setLoading(false)
                            navigation.navigate('Login')
                        }
                        else {
                            setLoading(false)
                            alert("Por favor tente novamente");
                        }
                    }
                )
        }
    }
    return (
        <View style={{ width: '100%', height: '100%'}}>
        <StatusBar hidden={true}/>
       <TouchableOpacity onPress={() => navigation.navigate('Login')} >

          <Image style={{alignSelf: 'flex-end', marginTop: 20, marginEnd: 20, width: 20, height: 20}} source={require('../../images/saircards.png')}/>

       </TouchableOpacity>
         
       <View style={{width: '100%', justifyContent: 'center', alignItems: 'center', marginTop: 50}}>
            
            <Text  style={{fontSize: 25, fontWeight: 'bold'}}>Crie uma nova senha</Text>

            
            <TextInput style={{width: '95%', height: 40, borderColor: 'gray', borderWidth: 1, borderRadius: 5, marginTop: 20, backgroundColor: 'white',
            padding: 10}}
            
            placeholder="Senha"  secureTextEntry
                onChangeText={(text) => setSenha(text)}
            />
            <TextInput style={{width: '95%', height: 40, borderColor: 'gray', borderWidth: 1, borderRadius: 5, marginTop: 20, backgroundColor: 'white',
            padding: 10}}
            
            placeholder="Confirme sua senha" secureTextEntry
                onChangeText={(text) => setConfirmsenha(text)}
            />
          <TouchableOpacity  style={{width: '95%', height: 40, borderRadius: 5, marginTop: 20, backgroundColor: '#ec230d',
                color: 'white', fontSize: 18, alignItems: 'center', justifyContent: 'center'}} onPress={() => handlePassword()}>
                <Text style={{fontSize: 15, color: 'white', fontWeight: 'bold'}}
                    
                >
                    Concluir o cadastro
                </Text>
                </TouchableOpacity>
        </View>
        </View>
    )
}



export default Registrarnovasenha

const styles = StyleSheet.create({})